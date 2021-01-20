import React, {ReactNode, PureComponent} from "react";
import "./styles.scss";
import View from "../../../components/View";
import PageHeader from "../../../components/PageHeader";
import Button from "../../../ui/Button";
import { formatDate } from "../../../libs";
import {
	Table,
	TableCell,
	TableHead,
	TableRow,
	TableSortCell,
	TableRowPlaceholder,
	TableBodyEmpty
} from "../../../ui/Table";
import More from "../../../ui/More";
import { Tabs, TabsItem } from "../../../ui/Tabs";
import Pagination from "../../../ui/Pagination";
import {RouteComponentProps, withRouter} from "react-router-dom";
import { Breakpoint } from "../../../libs/Breakpoint";
import SingleField from "../../../components/Field/SingleField";
import {observer} from "mobx-react";
import usersStore from "../usersStore";
import { withTranslation, WithTranslation } from "react-i18next";
import Tooltip from "../../../ui/Tooltip";
import {TStaffStatus} from "../../../stores/StaffStore/types";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_DELETE} from "../../../stores/StaffStore/config";
import UsersMessages from "../UsersMessages";
import {createSortOptions, createSortableTableHead, TSortConfig} from "../../../libs/sortOptions";
import uuid from "uuid/v4";
import {printUserRole, printUserStatus} from "../../../config/dictionary";
import PageTitle from "../../../components/PageTitle";
import QueryController from "../../../stores/ListStore/QueryController";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import {isDate} from "../../../libs/isDate";


type Props = {
	children?: ReactNode,
	data?: any
} & RouteComponentProps & WithTranslation

type State = {
	error: boolean,
}

class UsersList extends PureComponent<Props, State> {
	state = {
		error: false,
	};

	sortConfig: TSortConfig = [
		{ value: "createdAt", title: this.props.t("Created at"), id: uuid() },
		{ value: "status", title: this.props.t("Status"), id: uuid() },
		{ value: "name", title: this.props.t("Full name"), id: uuid() },
		{ value: "role", title: this.props.t("Access role"), id: uuid() },
		{ value: "lastLoggedAt", title: this.props.t("Recent activity"), id: uuid() },
		{ id: uuid() }
	];

	async componentDidMount() {
		await this.updateData();
	}

	async componentDidUpdate(prevProps: any, prevState: any) {
		if(this.props.location.search !== prevProps.location.search) {
			await this.updateData();
		}
	}

	render() {
		const { list, allUserCount, activeCount, blockedCount } = usersStore;
		const { t } = this.props;

		return (
			<View className="m-users-list">
				<PageTitle contentString={t("Users list")} />
				<PageHeader back="/company" title={ t("Users list") }>
					<Button type="alt" to="/users/create">{ t("Create user") }</Button>
				</PageHeader>

				<div className="m-users-list__body">
					<Tabs
						onChange={this.updateList.toTab("status")}
						pending={list.pending}
						defaultValue="all"
						activeValue={list.getParam("status")}
					>
						<TabsItem value="all">{ t("All users") } { allUserCount || "" } </TabsItem>
						<TabsItem value="active">{ t("Active users") } { activeCount || "" }</TabsItem>
						<TabsItem value="blocked">{ t("Blocked users") } { blockedCount || "" }</TabsItem>
					</Tabs>

					<Table withMore colCount={6} grid={[[2,1],2,[2,1],40]}>
						<Breakpoint range={["mobile", "tablet"]}>
							<SingleField
								key={`${list.params.sort}_${list.params.order}`}
								value={ `${list.params.sort}_${list.params.order}` }
								onChange={this.updateList.toOrderedSort}
								options={{
									defaultLabel: t("Select the sorting for the table"),
									selectOptions: createSortOptions(this.sortConfig),
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
							!list.pending
								? (list.items && list.items.length > 0)
									? list.items.map((item) => {
										const { createdAt, status, role, lastLoggedAt } = item.value;
										const fullName = usersStore.staff.getFullName(item.value);
										const isBlocked = status === "blocked";

										return (
											<TableRow key={item.id}>
												<TableCell label={ t("Created at") }>{ formatDate({ date: createdAt, formatKey: "date" }) }</TableCell>
												<TableCell label={ t("Status") } type={ isBlocked ? "error" : null }>{ printUserStatus(status) }</TableCell>
												<TableCell label={ t("Full name") } type="disabled">
													<Tooltip message={ fullName.long } tooltipKey={item.id}>
														{ fullName.short }
													</Tooltip>
												</TableCell>
												<TableCell label={ t("Access role") } type="disabled">{ printUserRole(role) }</TableCell>
												<TableCell label={ t("Recent activity") } type="disabled">
													{ isDate(lastLoggedAt.toString()) ? formatDate({ date: lastLoggedAt, formatKey: "datetime" }) : "-" }
												</TableCell>
												<TableCell>
													<More actions={this.createUserActions(item.id, status)} />
												</TableCell>
											</TableRow>
										);
									})
									: <TableBodyEmpty message={t("No users")} />
								: <TableRowPlaceholder count={6} />
						}
					</Table>

					<Pagination
						disabled={list.pending}
						data={list.pagination}
						urlParam="users"
						onPageChange={this.updateList.toPage} />
				</div>
				<UsersMessages afterChangeStatus={this.afterSensitiveOk} afterDelete={this.afterSensitiveOk} />
			</View>
		);
	}

	updateList = createListUpdater(usersStore.list, this.props.history, this.props.location);

	createUserActions = (id: string, status: TStaffStatus) => ([
		{
			icon: "edit",
			title: this.props.t("Edit user"),
			handler: () => this.props.history.push("/users/edit/" + id)
		},
		{
			icon: "lock",
			title: status === "active" ? this.props.t("Block user") : this.props.t("Unblock user"),
			handler: () => usersStore.staff.requestAction(STAFF_ACTION_CHANGE_STATUS, id, { status })
		},
		{
			icon: "trash",
			title: this.props.t("Remove user"),
			handler: () => usersStore.staff.requestAction(STAFF_ACTION_DELETE, id)
		}
	]);

	updateData = async () => {
		await usersStore.list.updateData(QueryController.getParamsFromSearch(this.props.location.search, []));
	};

	afterSensitiveOk = async () => {
		usersStore.staff.clearActionState();
		await this.updateData();
	}
}

export default withTranslation()(withRouter(observer(UsersList)));
