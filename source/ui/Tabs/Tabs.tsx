import React, {ReactNode} from "react";
import classNames from "classnames";
import { Label } from "../Typography";
import "./styles.scss";

type TItemValue = string;

type Props = {
	children?: ReactNode,
	onChange: (value: TItemValue) => void,
	type?: "full" | "auto",
	pending?: boolean,
	activeValue: TItemValue,
	defaultValue: TItemValue,
	className?: string
}

type ItemProps = {
	children?: ReactNode,
	value: TItemValue,
	disabled?: boolean,
	readonly onClick?: () => void,
	readonly active?: boolean,
}

const Tabs = ({ className, children, activeValue, defaultValue, pending, onChange, type = "full" }: Props) => {
	const classes = classNames("c-tabs", `c-tabs--${type}`, {
		"is-pending": typeof pending !== "undefined" ? pending : false,
		[className as string]: className
	});

	const tabs = React.Children.map(children, (child: ReactNode) => {

		if (!React.isValidElement(child)) {
			return null;
		}
		const isActive = activeValue ? child.props.value === activeValue : child.props.value === defaultValue;
		const isDisabled = child.props.disabled;
		const onClick = (pending || isActive || isDisabled) ? () => {} : () => onChange(child.props.value);

		return React.cloneElement(child, {
			...child.props,
			active: isActive,
			onClick: onClick
		});
	});

	return (
		<div className={classes}>
			{tabs}
		</div>
	);
};

const TabsItem = ({ children, disabled, active, onClick }: ItemProps) => {

	const classes = classNames("c-tabs__item", {
		"is-active": active,
		"is-disabled": disabled
	});

	return (
		<div role="button" className={classes} onClick={onClick}>
			<Label>{children}</Label>
		</div>
	);
};

export { Tabs, TabsItem };
