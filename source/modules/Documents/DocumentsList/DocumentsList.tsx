import React, {Component, ReactNode} from "react";
import "./styles.scss";
import PageHeader from "../../../components/PageHeader";
import Button from "../../../ui/Button";
import View from "../../../components/View";
import { withTranslation, WithTranslation } from "react-i18next";
import {
	Table,
	TableCell,
	TableHead, TableRow,
	TableRowPlaceholder,
	TableSortCell
} from "../../../ui/Table";
import SingleField from "../../../components/Field/SingleField";
import {formatDate, printFormattedSum, propValidate} from "../../../libs";
import {Breakpoint} from "../../../libs/Breakpoint";
import {RouteComponentProps, withRouter} from "react-router";
import { observer } from "mobx-react";
import documentsStore from "../documentsStore";
import Pagination from "../../../ui/Pagination";
import FileLink from "../../../ui/FileLink";
import {createSortableTableHead, createSortOptions, TSortConfig} from "../../../libs/sortOptions";
import uuid from "uuid/v4";
import {printDocumentStatus, printDocumentType} from "../../../config/dictionary";
import PageTitle from "../../../components/PageTitle";
import QueryController from "../../../stores/ListStore/QueryController";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import DocumentsTabs from "../DocumentsTabs";

type Props = {
	children?: ReactNode
} & WithTranslation & RouteComponentProps

class DocumentsList extends Component<Props> {

	async componentDidMount() {
		await documentsStore.list.updateData(QueryController.getParamsFromSearch(this.props.location.search, []));
	}

	async componentDidUpdate(prevProps: any, prevState: any) {
		if (this.props.location.search !== prevProps.location.search) {
			await documentsStore.list.updateData(QueryController.getParamsFromSearch(this.props.location.search, []));
		}
	}

	sortConfig: TSortConfig = [
		{ title: this.props.t("Created at"), value: "createdAt", id: uuid() },
		{ title: this.props.t("Type"), id: uuid() },
		{ title: this.props.t("Number"), id: uuid() },
		{ title: this.props.t("Sum"), id: uuid() },
		{ title: this.props.t("File"), id: uuid() },
		{ title: this.props.t("Status"), id: uuid() }
	];

	render() {
		const { list } = documentsStore;
		const { t } = this.props;

		return (
			<View className="m-documents-list">
				<PageTitle contentString={ t("Documents") } />
				<PageHeader title={t("Documents")}>
					<Button type="alt" to="/documents/act">{ t("Request Act") }</Button>
					<Button type="alt" to="/documents/invoice">{ t("Request invoice") }</Button>
				</PageHeader>

				<div className="m-documents-list__body">
					<DocumentsTabs tab="list" />
					<Table colCount={6} grid={[ 130, [2, 150], 130, 1, 190 ]}>

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
								const { createdAt, amount, file, number, status, type } = item.value;

								return (
									<TableRow key={`table_row_${item.id}`}>
										<TableCell label={ t("Created at") }>{formatDate({date: createdAt, formatKey: "datetime"})}</TableCell>
										<TableCell label={ t("Document type")}>{ printDocumentType(type) }</TableCell>
										<TableCell label={ t("Number")}>{ this.printNum(number) }</TableCell>
										<TableCell label={ t("Sum") }> { this.printAmount(amount) }</TableCell>
										<TableCell label={ t("File") } isWidget={true}>
											<FileLink file={file}/>
										</TableCell>
										<TableCell label={ t("Status") }>{ printDocumentStatus(status) }</TableCell>
									</TableRow>
								);
							})
						}
					</Table>

					<Pagination
						disabled={list.pending}
						data={list.pagination}
						urlParam="/documents"
						onPageChange={this.updateList.toPage}/>
				</div>
			</View>
		);
	}
	updateList = createListUpdater(documentsStore.list, this.props.history, this.props.location);


	printNum = (number: string) => propValidate<string>( number, (val) => val.length > 0, "-" );
	printAmount = (amount: string) => propValidate<number, string>(
		parseInt(amount, 10),
		(val) => val !== 0,
		"-",
		printFormattedSum
	);
}

export default withTranslation()(withRouter(observer(DocumentsList)));
