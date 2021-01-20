import React, {Component, Fragment, ReactNode, useState} from "react";
import "./styles.scss";
import {RouteComponentProps, withRouter} from "react-router";
import {useTranslation, withTranslation, WithTranslation} from "react-i18next";
import driversStore from "../driversStore";
import PageHeader from "../../../components/PageHeader";
import Button from "../../../ui/Button";
import {Tabs, TabsItem} from "../../../ui/Tabs";
import View from "../../../components/View";
import Pagination from "../../../ui/Pagination";
import {Breakpoint} from "../../../libs/Breakpoint";
import SingleField from "../../../components/Field/SingleField";
import {
	Table,
	TableBodyEmpty,
	TableCell,
	TableHead,
	TableRow,
	TableRowPlaceholder,
	TableSortCell
} from "../../../ui/Table";
import More from "../../../ui/More";
import {observer} from "mobx-react";
import {Caption, Label, Paragraph} from "../../../ui/Typography";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_DELETE} from "../../../stores/StaffStore/config";
import Tooltip from "../../../ui/Tooltip";
import {createSortableTableHead, createSortOptions, TSortConfig} from "../../../libs/sortOptions";
import uuid from "uuid/v4";
import {printDriverStatus} from "../../../config/dictionary";
import QueryController from "../../../stores/ListStore/QueryController";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import {TStaffStatus} from "../../../stores/StaffStore/types";
import DriversMessages from "../DriversMessages";
import Icon from "../../../ui/Icon";
import {TTableRowGrid} from "../../../ui/Table/Table";

type Props = {
	children?: ReactNode,
	data?: any
} & RouteComponentProps & WithTranslation

type State = {
	error: boolean,
}

const StringsList = ({items} :{ items: { number: string }[] }) => {
	return (
		<Fragment>
			{
				(!items || items.length === 0)
					? "-"
					: items.map((item, index) => (<Fragment key={index}>{ item.number } {(index < items.length - 1) && <br />}</Fragment>))
			}
		</Fragment>
	);
};

const LongName = ({ content }: {content: string}) => {
	const [open, setOpen] = useState<boolean>(false);
	const { t } = useTranslation();

	const LIMIT = 90;
	const needButton = content.length > LIMIT && !open;
	const preparedContent = needButton ? content.substring(0, LIMIT) + "... " : content;

	return (
		<Paragraph className="c-table__cell-content" as="span">
			{ preparedContent }
			{ needButton && <Label className="c-table__show-more" onClick={() => setOpen(true)} color="darkgrey">{ t("Show more")} <Icon type="triangle-down" /></Label> }
		</Paragraph>
	);
};

class DriversList extends Component<Props>{
	state: State = {
		error: false
	};

	sortConfig: TSortConfig = [
		{ value: "fullName", title: this.props.t("Name"), id: uuid() },
		{ title: this.props.t("Car number"), id: uuid() },
		{ value: "email", title: this.props.t("Email"), id: uuid() },
		{ title: this.props.t("Phone"), id: uuid() },
		{ value: "status", title: this.props.t("Status"), id: uuid() },
		{ value: "note", title: this.props.t("Notes"), id: uuid() },
		{ id: uuid() }
	];

	rowGrid: TTableRowGrid = [ 1, 100, 1, 140, 80, 1, 40];

	async componentDidMount() {
		await driversStore.list.updateData(QueryController.getParamsFromSearch(this.props.location.search, []));
	}

	async componentDidUpdate(prevProps: any, prevState: any) {
		if(this.props.location.search !== prevProps.location.search) {
			await driversStore.list.updateData(QueryController.getParamsFromSearch(this.props.location.search, []));
		}
	}

	render() {

		const { list, staff } = driversStore;
		const { t } = this.props;

		return (
			<View className="m-drivers-list">
				<PageHeader back="/company" title={ t("Drivers") }>
					<Button type="alt" to="/drivers/create">{ t("Create driver") }</Button>
				</PageHeader>

				<div className="m-drivers-list__body">
					<div className="m-drivers-list__tabs">
						<Caption>{t("Driver status")}:</Caption>
						<Tabs
							type="auto"
							pending={list.pending}
							activeValue={list.getParam("status")}
							defaultValue="all"
							onChange={this.updateList.toTab("status")}
						>
							<TabsItem value="all">{ t("All") }</TabsItem>
							<TabsItem value="active">{ t("Active drivers") }</TabsItem>
							<TabsItem value="blocked">{ t("Blocked drivers") }</TabsItem>
						</Tabs>
					</div>

					<Table withMore colCount={6} grid={this.rowGrid}>
						<Breakpoint range={["mobile", "tablet"]}>
							<SingleField
								key={`${list.params.sort}_${list.params.order}`}
								value={ `${list.params.sort}_${list.params.order}` }
								onChange={this.updateList.toOrderedSort}
								options={{
									defaultLabel: t("Select the sorting for the table"),
									selectOptions: createSortOptions(this.sortConfig)
								}}
								label={t("Sort")} type="Select" />
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
								: list.items && list.items.length > 0
									? list.items.map((item) => {

											const { carsNumbers, phones, status, email, note } = item.value;
											const fullName = staff.getFullName(item.value);

											return (
												<TableRow key={item.id}>
													<TableCell label={ t("Name") }>
														<Tooltip size="extra" message={fullName.long} tooltipKey={item.id} ellipsis={true}>
															{ fullName.short }
														</Tooltip>
													</TableCell>
													<TableCell label={ t("Car number") }>
														<StringsList items={carsNumbers} />
													</TableCell>
													<TableCell type="overflow-break" label={ t("Email") }>
														{email}
													</TableCell>
													<TableCell label={ t("Phone") } type={"bold"}>
														<StringsList items={phones} />
													</TableCell>
													<TableCell label={ t("Status") } type={ status === "blocked" ? "error" : null }>
														{ printDriverStatus(status) }
													</TableCell>
													<TableCell type="long-name" label={ t("Notes") } isWidget>
														{note ? <LongName content={note} /> : "-"}
													</TableCell>
													<TableCell>
														<More actions={this.createDriverActions(item.id, status)} />
													</TableCell>
												</TableRow>
											);
										})
								: <TableBodyEmpty message={ t("Not found drivers") } />
						}
					</Table>

					<Pagination
						disabled={list.pending}
						data={list.pagination}
						urlParam="drivers"
						onPageChange={this.updateList.toPage} />
				</div>
				<DriversMessages
					key="drivers_list_messages"
					afterChangeStatus={this.afterSensitiveOk}
					afterDelete={this.afterSensitiveOk}
				/>
			</View>
		);
	}

	updateList = createListUpdater(driversStore.list, this.props.history, this.props.location);

	createDriverActions = (id: string, status: TStaffStatus) => ([
		{
			icon: "edit",
			title: this.props.t("Edit driver"),
			handler: () => this.props.history.push("/drivers/edit/" + id)
		},
		{
			icon: "lock",
			title: status === "active" ? this.props.t("Block driver") : this.props.t("Unblock driver"),
			handler: () => driversStore.staff.requestAction(STAFF_ACTION_CHANGE_STATUS, id, { status })
		},
		{
			icon: "trash",
			title: this.props.t("Remove driver"),
			handler: () => driversStore.staff.requestAction(STAFF_ACTION_DELETE, id)
		}
	]);

	updateData = async () => {
		await driversStore.list.updateData(QueryController.getParamsFromSearch(this.props.location.search, []));
	};

	afterSensitiveOk = async () => {
		driversStore.staff.clearActionState();
		await this.updateData();
	}
}

export default withTranslation()(withRouter(observer(DriversList)));
