import React, {Component, ReactNode} from "react";
import {Table, TableCell, TableHead, TableRow, TableSortCell, TableRowPlaceholder } from "../../../ui/Table";
import PageHeader from "../../../components/PageHeader";
import View from "../../../components/View";
import Pagination from "../../../ui/Pagination";
import {formatDate, logger, normalizeAmount, printFormattedSum, propOf} from "../../../libs";
import {Breakpoint} from "../../../libs/Breakpoint";
import SingleField from "../../../components/Field/SingleField";
import transactionsCardsStore from "../transactionsCardsStore";
import {observer} from "mobx-react";
import {RouteComponentProps, withRouter} from "react-router-dom";
import TransactionsTabs from "../TransactionsTabs/";
import TransactionsBalance from "../TransactionsBalance";
import { withTranslation, WithTranslation } from "react-i18next";
import uuid from "uuid/v4";
import {createSortOptions, createSortableTableHead, TSortConfig} from "../../../libs/sortOptions";
import {printTransactionStatus} from "../../../config/dictionary";
import PageTitle from "../../../components/PageTitle";
import {createFiltersForm} from "./createFiltersForm";
import Filters from "../../../components/Filters";
import {dataFromFilters, dataToFilters} from "./helpers";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import QueryController from "../../../stores/ListStore/QueryController";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import {TLabelsMapping} from "../../../components/Filters/Filters";

type Props = {
	children?: ReactNode
} & RouteComponentProps & WithTranslation

const transactionsFiltersKeys = {
	single: ["dateTo", "dateFrom", "cardNumber", "status"],
	multi: ["regions", "supplies", "supplyTypes", "networkStations"],
	get all(): string[] {
		return [...transactionsFiltersKeys.multi, ...transactionsFiltersKeys.single];
	}
};

class TransactionsCards extends Component<Props> {

	sortConfig: TSortConfig = [
		{ value: "createdAt", title: this.props.t("Created at"), id: uuid() },
		{ value: "cardNumber", title: this.props.t("Card number"), id: uuid() },
		{ value: "fuelName", title: this.props.t("Fuel"), id: uuid() },
		{ value: "volume", title: this.props.t("Volume, l"), id: uuid() },
		{ value: "price", title: this.props.t("Price"), id: uuid() },
		{ value: "amount", title: this.props.t("Sum"), id: uuid() },
		{ value: "networkStation", title: this.props.t("Station"), id: uuid() },
		{ value: "status", title: this.props.t("Type"), id: uuid() },
	];

	filtersConfig: TLabelsMapping = {
		prefixes: {
			cardNumber: this.props.t("Card number"),
			status: this.props.t("Status"),
			dateFrom: this.props.t("Date from"),
			dateTo: this.props.t("Date to")
		},
		translates: {
			status: {
				"write-off": this.props.t("Write off"),
				"return": this.props.t("Return"),
				"replenishment": this.props.t("Replenishment")
			}
		}
	}


	async componentDidMount() {
		const params = QueryController.getParamsFromSearch(this.props.location.search, transactionsFiltersKeys.multi);
		await transactionsCardsStore.list.updateData(params);
		transactionsCardsStore.filters.init(params, transactionsFiltersKeys.all);
	}

	async componentDidUpdate(prevProps: any, prevState: any) {
		if (this.props.location.search !== prevProps.location.search) {
			await transactionsCardsStore.list.updateData(
				QueryController.getParamsFromSearch(this.props.location.search, transactionsFiltersKeys.multi)
			);
		}
	}

	componentWillUnmount() {
		transactionsCardsStore.filters.reset();
	}

	render() {
		const { list, filters, reportService, filteredData } = transactionsCardsStore;
		const { getApplied: getAppliedFilters } = transactionsCardsStore.filters;
		const { t } = this.props;
		const scoreValue = propOf<string>(transactionsCardsStore.list.metaInfo, "accountBalance", "--");
		return (

			<View className="m-transactions-list">
				<PageTitle contentString={t("Card transactions")} />
				<PageHeader
					title={ t("Card transactions") }
					lead={ t("This displays all the transactions recorded in your account.") }>
					<TransactionsBalance accountBalance={scoreValue}/>

				</PageHeader>
				<div className="m-transactions-list__body">
					<TransactionsTabs tab="cards" />

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

					<Table colCount={7} grid={[120, 90, 140, 90,80, 100, 3, 100]}>

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

								const { status,createdAt, cardNumber, fuelName, amount, networkStation, volume, price } = item.value;

								return (
									<TableRow key={`table_row_${item.id}`}>
										<TableCell label={ t("Created at")}>{formatDate({date: createdAt, formatKey: "datetime"})}</TableCell>
										<TableCell label={ t("Card number")}>{cardNumber}</TableCell>
										<TableCell label={ t("Fuel") }>{fuelName}</TableCell>
										<TableCell label={ t("Volume, l") }> {normalizeAmount(volume)} </TableCell>
										<TableCell label={ t("Price") }>{ normalizeAmount(price) }{" "}{ t("uah/l") }</TableCell>
										<TableCell label={ t("Sum") }>{ printFormattedSum(amount) }</TableCell>
										<TableCell label={ t("Station") } type="long-name">{
											networkStation
												.split(".").join(". ")
												.split(",").join(", ")
												.replace(/\s\s/, " ")
										}</TableCell>
										<TableCell label={ t("Type")}>{ printTransactionStatus(status) }</TableCell>
									</TableRow>
								);
							})
						}
					</Table>

					<Pagination
						disabled={list.pending}
						data={list.pagination}
						urlParam="/transactions/cards"
						onPageChange={this.updateList.toPage}/>
				</div>
			</View>
		);
	}

	submitFiltersHandler = (data: TSimpleFormData) => {
		const { submit: submitFilters, getApplied: getAppliedFilters } = transactionsCardsStore.filters;
		const formattedFilters = dataToFilters(data);
		logger("formatted Filters", formattedFilters);

		submitFilters(formattedFilters);
		this.updateList.toFilter(getAppliedFilters);
	};

	updateList = createListUpdater(transactionsCardsStore.list, this.props.history, this.props.location);
	updateFilters = () => this.updateList.toFilter(transactionsCardsStore.filters.getApplied);
}

export default withTranslation()(withRouter(observer(TransactionsCards)));
