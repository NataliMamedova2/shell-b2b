import React from "react";
import "./styles.scss";
import ReactPaginate from "react-paginate";
import classNames from "classnames";
import {useBreakpoint} from "../../libs/Breakpoint";

type Props = {
	disabled: boolean,
	urlParam: string,
	onPageChange: (selected: number) => void,
	data: TPaginationData | null
}

type TPaginationData = {
	currentPage: number,
	totalCount: number,
}

const defaultProps = {
	data: {
		currentPage: 1,
		totalCount: 1,
	}
};

const Pagination = ({disabled, data, urlParam, onPageChange}: Props) => {
	const { state: { isMobile } } = useBreakpoint();
	const classes = classNames("c-pagination__wrapper", {
		"is-disabled": disabled
	});

	return (
		<div className="c-pagination">

			{
				(!data || data.totalCount <= 1)
					? null
					: (
						<ReactPaginate
							containerClassName={classes}
							activeClassName="is-active"
							disabledClassName="is-disabled"
							pageClassName="c-pagination__item"
							pageLinkClassName="c-pagination__link"
							nextClassName="c-pagination__control"
							previousClassName="c-pagination__control"
							nextLinkClassName="c-icon c-icon--chevron-right"
							previousLinkClassName="c-icon c-icon--chevron-left"
							breakClassName="c-pagination__item is-disabled"
							previousLabel=""
							nextLabel=""
							forcePage={data.currentPage - 1}
							pageCount={data.totalCount}
							hrefBuilder={(page) => {
								return page === 1 ? `/${urlParam}` : `/${urlParam}?page=${page}`;
							}}
							initialPage={data.currentPage - 1}
							// pageRangeDisplayed={ isMobile ? 1 : 2}
							pageRangeDisplayed={ isMobile ? 1 : 3}
							disableInitialCallback={true}
							marginPagesDisplayed={1}
							onPageChange={({ selected }) => onPageChange(selected)}
						/>
					)
			}


		</div>
	);
};

Pagination.defaultProps = defaultProps;

export default Pagination;
