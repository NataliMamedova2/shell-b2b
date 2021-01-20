import React, {Component, ReactNode} from "react";
import {Table, TableCell, TableHead, TableRow, TableSortCell, TableRowPlaceholder} from "../../../ui/Table";
import PageHeader from "../../../components/PageHeader";
import View from "../../../components/View";
import Pagination from "../../../ui/Pagination";
import {formatDate, logger, printFormattedSum, propOf} from "../../../libs";
import {Breakpoint} from "../../../libs/Breakpoint";
import SingleField from "../../../components/Field/SingleField";
import transactionsCompanyStore from "../transactionsCompanyStore";
import {observer} from "mobx-react";
import {RouteComponentProps, withRouter} from "react-router-dom";
import TransactionsTabs from "../TransactionsTabs";
import TransactionsBalance from "../TransactionsBalance";
import { withTranslation, WithTranslation } from "react-i18next";
import {createSortableTableHead, createSortOptions, TSortConfig} from "../../../libs/sortOptions";
import uuid from "uuid/v4";
import {TTableRowGrid} from "../../../ui/Table/Table";
import {printTransactionType} from "../../../config/dictionary";
import PageTitle from "../../../components/PageTitle";
import QueryController from "../../../stores/ListStore/QueryController";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import {dataFromFilters, dataToFilters} from "../TransactionsCards/helpers";
import {createFiltersForm} from "./createFiltersForm";
import Filters from "../../../components/Filters";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import {TLabelsMapping} from "../../../components/Filters/Filters";

type Props = {
	children?: ReactNode
} & RouteComponentProps & WithTranslation

const transactionsFiltersKeys = {
	single: ["dateTo", "dateFrom", "type"],
	multi: [],
	get all(): string[] {
		return [...transactionsFiltersKeys.multi, ...transactionsFiltersKeys.single];
	}
};

class TransactionsCompany extends Component<Props> {

	sortConfig: TSortConfig = [
		{ value: "createdAt", title: this.props.t("Created at"), id: uuid() },
		{ value: "type", title: this.props.t("Transaction type"), id: uuid() },
		{ value: "amount", title: this.props.t("Sum"), id: uuid() },
	];
	tableRowGrid: TTableRowGrid = [[3,1]];

	filtersConfig: TLabelsMapping = {
		prefixes: {
			type: this.props.t("Type"),
			dateFrom: this.props.t("Date from"),
			dateTo: this.props.t("Date to")
		},
		translates: {
			type: {
					"write-off-cards": this.props.t("Write off"),
					"refill": this.props.t("Refill"),
					"discount": this.props.t("Discount")
			}
		}
	}

	async componentDidMount() {
		const params = QueryController.getParamsFromSearch(this.props.location.search, transactionsFiltersKeys.multi);
		await transactionsCompanyStore.list.updateData(params);
		transactionsCompanyStore.filters.init(params, transactionsFiltersKeys.all);
	}

	async componentDidUpdate(prevProps: any, prevState: any) {
		if (this.props.location.search !== prevProps.location.search) {
			await transactionsCompanyStore.list.updateData(
				QueryController.getParamsFromSearch(this.props.location.search, [])
			);
		}
	}

	componentWillUnmount() {
		transactionsCompanyStore.filters.reset();
	}

	render() {
		const { list, filteredData, reportService, filters } = transactionsCompanyStore;
		const { getApplied: getAppliedFilters } = transactionsCompanyStore.filters;
		const { t } = this.props;
		const scoreValue = propOf<string>(transactionsCompanyStore.list.metaInfo, "accountBalance", "--");

		return (
			<View className="m-transactions-list">
				<PageTitle contentString={t("Company transactions")} />
				<PageHeader
					title={ t("Company transactions")}
					lead={ t("This displays all the transactions recorded in your account.") }>
					<TransactionsBalance accountBalance={scoreValue}/>
				</PageHeader>

				<div className="m-transactions-list__body">
					<TransactionsTabs tab="company" />

					<Filters
						appliedLabelsMapping={this.filtersConfig}
						pending={list.pending}
						filtersData={filteredData}
						storedData={dataFromFilters(getAppliedFilters())}
						filters={filters}
						reportService={reportService}
						configFactory={createFiltersForm}
						onApply={this.submitFiltersHandler}
						onClear={this.updateFilters}
						onRemove={this.updateFilters}
					/>

					<Table colCount={4} grid={this.tableRowGrid}>

						<Breakpoint range={["mobile", "tablet"]}>
							<SingleField
								value={ `${list.params.sort}_${list.params.order}` }
								onChange={this.updateList.toOrderedSort}
								options={{
									defaultLabel: t("Select the sorting for the table"),
									selectOptions: createSortOptions(this.sortConfig)
								}}
								label={t("Sort")} type="Select"/>
						</Breakpoint>

						<TableHead
							currentSort={list.getParam("sort")}
							sortOrder={list.getParam("order")}
							onSort={this.updateList.toSort}
						>
							{ createSortableTableHead(this.sortConfig, TableSortCell) }
						</TableHead>

						{
							list.pending
								? <TableRowPlaceholder count={6} />
								: list.items && list.items.map((item) => {

								const { createdAt, amount, type } = item.value;

								return (
									<TableRow key={item.id}>
										<TableCell label={ t("Created at") }>{formatDate({date: createdAt, formatKey: "datetime"})}</TableCell>
										<TableCell label={ t("Transaction type")}>{printTransactionType(type)}</TableCell>
										<TableCell label={ t("Sum") }> {printFormattedSum(amount)}</TableCell>
									</TableRow>
								);
							})
						}
					</Table>

					<Pagination
						disabled={list.pending}
						data={list.pagination}
						urlParam="/transactions/company"
						onPageChange={this.updateList.toPage}/>
				</div>
			</View>
		);
	}

	updateList = createListUpdater(transactionsCompanyStore.list, this.props.history, this.props.location);

	submitFiltersHandler = (data: TSimpleFormData) => {
		const { submit: submitFilters, getApplied: getAppliedFilters } = transactionsCompanyStore.filters;
		const formattedFilters = dataToFilters(data);
		logger("formatted Filters", formattedFilters);

		submitFilters(formattedFilters);
		this.updateList.toFilter(getAppliedFilters);
	};
	
	updateFilters = () => this.updateList.toFilter(transactionsCompanyStore.filters.getApplied);
}

export default withTranslation()(
	withRouter(
		observer(TransactionsCompany)
	)
);
