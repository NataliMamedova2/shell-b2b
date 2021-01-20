import React, {ReactNode, Fragment} from "react";
import classNames from "classnames";
import {Link} from "react-router-dom";
import "./styles.scss";
import Icon from "../Icon";
import {Note, H4, Paragraph} from "../Typography";

/**
 * Single number
 * b -> minmax(0, b(px|fr))
 * 2 numbers array
 * [a, b] -> repeat(a, b(px|fr))
 *
 * b <= 9 = [b]fr
 * b > 9 = [b]px
 */
export type TTableRowGrid = Array<number | [number, number]>;

type Props = {
	colCount?: number,
	children: ReactNode,
	grid?: TTableRowGrid,
	withMore?: boolean
}

type RowProps = {
	children: ReactNode,
	grid?: string,
	to?: string,
	className?: string
}

type CellProps = {
	children?: ReactNode,
	label?: string,
	isWidget?: boolean,
	type?: "disabled" | "error" | "widget" | string | null
}


type PlaceholderProps = {
	count: number
}

function generateRowGrid ({template, defaultTemplate}: { template?: TTableRowGrid, defaultTemplate: string }): string {
	if(!template || !Array.isArray(template)) {
		return defaultTemplate;
	}
	return template.reduce((res, current): string => {
		const currentValue = Array.isArray(current)
			? current[1] > 9
				? `repeat(${current[0]}, ${current[1]}px)`
				: `repeat(${current[0]}, minmax(0, ${current[1]}fr))`
			: current > 9
				? `${current}px`
				:`minmax(0, ${current}fr)`;

		res += currentValue + " ";
		return res;

	}, "").trim();

}

const Table = ({colCount = 6, grid, withMore, children}: Props) => {
	const rowGrid = generateRowGrid({template: grid, defaultTemplate: `repeat(${colCount}, minmax(0, 1fr))` });

	const rows = React.Children.map(children, child => {

		if(!React.isValidElement(child)) return null;

		return React.cloneElement(child, {
			...child.props,
			grid: rowGrid,
		});

	});

	const classes = classNames("c-table", {
		"c-table--with-more": withMore
	});

	return (
		<div className={classes}>
			{ rows }
		</div>
	);
};



const TableRow = ({children, grid, to, className}: RowProps) => {
	const classes = classNames("c-table__row", {
		"is-actionable": to,
		[`has-cells-${React.Children.count(children)}`]: true,
		[className as string]: className
	});


	if(to) {
		return <Link to={to} className={classes} style={{ gridTemplateColumns: grid }}>{children}</Link>;
	}

	return (
		<div className={classes} style={{ gridTemplateColumns: grid }}>{children}</div>
	);
};

const TableCell = ({children, type, label, isWidget = false}: CellProps) => {

	const classes = classNames("c-table__cell", {
		[`is-${type}`]: type
	});

	return (
		<div className={classes}>
			{ label && <Note className="c-table__label">{label}</Note> }
			{ isWidget ? children : <Paragraph className="c-table__cell-content" as="span">{children}</Paragraph> }
		</div>
	);
};

const TableSortIcon = ({ order }: { order?: string}) => {
	const classes = classNames("c-table__sort-icon", "c-icon", `is-${order}`);

	return <span className={classes} />;

};


type HeadProps = {
	children: ReactNode,
	onSort?: (value: string) => void
	grid?: string,
	sortOrder?: string,
	currentSort?: string
}

const TableHead = React.memo(({children, grid, sortOrder, currentSort, onSort}: HeadProps) => {

	const cells = React.Children.map(children, (child: ReactNode) => {
		if (!React.isValidElement(child)) return null;

		const sortable = typeof child.props.value !== "undefined";

		const options = {
			...child.props,
			onClick: sortable && onSort ? () => onSort(child.props.value) : null,
			order: sortOrder,
			sortedBy: typeof currentSort !== "undefined" && currentSort === child.props.value
		};

		return React.cloneElement(child, options );
	});

	const classes = classNames("c-table__head", `has-cells-${cells.length}`);
	return (
		<div className={classes} style={{ gridTemplateColumns: grid }}>{cells}</div>
	);
});


type SortProps = {
	sortedBy?: boolean,
	order?: string
	value?: string
	onClick?: () => void,
	children?: string
}

const TableSortCell = ({  sortedBy, order, value, onClick, children }: SortProps) => {
	const classes = classNames("c-table__sort-button", {
		"is-sortable" : value,
		"is-active" : sortedBy,
		"is-reverse" : order === "desc"
	});

	return (
		<div className="c-table__sort" title={children}>
			<div className={classes} onClick={onClick}>
				<span className="c-table__sort-label">{children}</span>
				{ sortedBy && order ? <TableSortIcon order={order}/> : null }
			</div>
		</div>
	);
};

const TableCard = ({ children }: { children?: ReactNode }) => {
	return (
		<div className="c-table__card">
			{children}
		</div>
	);
};

const TableRowPlaceholder = ({ count }: PlaceholderProps) => {
	const fakeRows: string[] = Array(count - 1).fill("...");

	return (
		<Fragment>
			<TableRow className="c-table__row--placeholder"><TableCell><Icon pending={true} type="pending" /></TableCell></TableRow>
			{ fakeRows.map((i, index) => <TableRow className="c-table__row--placeholder" key={index}><TableCell>{i}</TableCell></TableRow>) }
		</Fragment>
	);
};
const TableCardPlaceholder = ({ count }: PlaceholderProps) => {
	const fakeRows: string[] = Array(count - 1).fill("...");

	return (
		<Fragment>
			<TableCard><TableCell><Icon pending={true} type="pending" /></TableCell></TableCard>
			{ fakeRows.map((i, index) => <TableCard key={index}><TableCell>{i}</TableCell></TableCard>) }
		</Fragment>
	);
};

const TableBodyEmpty = ({ message }: { message: string }) => {
	return (
		<div className="c-table__empty">
			<H4 color="darkgrey">{message}</H4>
		</div>
	);
};

export { Table, TableHead, TableRow, TableCell, TableSortCell, TableCard, TableRowPlaceholder, TableCardPlaceholder, TableBodyEmpty};
