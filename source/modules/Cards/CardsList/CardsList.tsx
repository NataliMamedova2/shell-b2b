import React, {Component, ReactNode } from "react";
import "./styles.scss";
import {RouteComponentProps, withRouter} from "react-router";
import {withTranslation, WithTranslation} from "react-i18next";
import cardsStore, {TCardItem} from "../cardsStore";
import PageHeader from "../../../components/PageHeader";
import Button from "../../../ui/Button";
import {Tabs, TabsItem} from "../../../ui/Tabs";
import View from "../../../components/View";
import {Breakpoint} from "../../../libs/Breakpoint";
import SingleField from "../../../components/Field/SingleField";
import {Table, TableCell, TableHead, TableRow, TableRowPlaceholder, TableSortCell, TableBodyEmpty} from "../../../ui/Table";
import More from "../../../ui/More";
import {observer} from "mobx-react";
import {Caption, Label, Paragraph} from "../../../ui/Typography";
import Icon from "../../../ui/Icon";
import CardsMessages from "../CardsMessages";
import {STAFF_ACTION_CHANGE_STATUS} from "../../../stores/StaffStore/config";
import {createSortOptions, createSortableTableHead, TSortConfig} from "../../../libs/sortOptions";
import uuid from "uuid/v4";
import {IItemController} from "../../../stores/ListStore/ItemController";
import {printCardStatus} from "../../../config/dictionary";
import {getFullName, logger, propOf} from "../../../libs";
import PageTitle from "../../../components/PageTitle";
import QueryController from "../../../stores/ListStore/QueryController";
import SimpleSearch from "../../../components/SimpleSearch";
import Pagination from "../../../ui/Pagination";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import {TTableRowGrid} from "../../../ui/Table/Table";

type Props = {
	children?: ReactNode,
	data?: any
} & RouteComponentProps & WithTranslation

type State = {
	error: boolean
}

const SEARCH_QUERY_PARAM = "queryString";

class CardsList extends Component<Props> {
	state: State = {
		error: false
	};

	sortConfig: TSortConfig = [
		{ value: "cardNumber", id: uuid(), title: this.props.t("Card number") },
		{ value: "status", id: uuid(), title: this.props.t("Card status") },
		{ id: uuid(), title: this.props.t("Driver") },
		{ id: uuid(), title: this.props.t("Cars") },
		{ id: uuid() },
	];

	rowGrid: TTableRowGrid = [ 200, 200, [2, 1], 40];

	async componentDidMount() {
		this.updateDataBySearch(this.props.location.search);
	}

	async componentDidUpdate(prevProps: any, prevState: any) {
		if (this.props.location.search !== prevProps.location.search) {
			this.updateDataBySearch(this.props.location.search);
		}
	}

	render() {
		const { list, activeCardsCount } = cardsStore;
		const { t } = this.props;


		return (
			<View className="m-cards-list">
				<PageTitle contentString={t("Fuel cards")} />
				<PageHeader title={ t("Fuel cards") }>

					<div className="m-cards-list__score">
						<Label>{ t("Active cards") }</Label>
						<Paragraph className="m-cards-list__count">{ list.pending ? <Icon type="pending" pending={true}  /> : activeCardsCount }</Paragraph>
					</div>

					<Button type="alt" to="/cards/create">{ t("Create new card") }</Button>
				</PageHeader>

				<div className="m-cards-list__body">

					<div className="m-cards-list__toolbar">

						<SimpleSearch
							initValue={this.getCardSearchParam()}
							onReset={this.updateSearch}
							placeholder={t("Enter card number or driver name")}
							onSearch={this.updateSearch} />

						<div className="m-cards-list__tabs">
							<Caption>{t("Card status")}:</Caption>
							<Tabs
								type="auto"
								pending={list.pending}
								onChange={this.updateList.toTab("status")}
								activeValue={list.getParam("status")}
								defaultValue="all"
							>
								<TabsItem value="all">{ t("All cards") }</TabsItem>
								<TabsItem value="active">{ t("Active cards") }</TabsItem>
								<TabsItem value="blocked">{ t("Blocked cards") }</TabsItem>
							</Tabs>
						</div>
					</div>


					<Table withMore grid={this.rowGrid}>
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
								: (list.items && list.items.length > 0)
									? list.items.map((item) => {
										const { cardNumber, status, onModeration } = item.value;

										const driverName = propOf(item.value, "driver", "-", data => getFullName(data).short);
										const driverCars = propOf<{number: string}[], string>(item.value, "driver.carsNumbers", "-" as any, (data) => data.map(i => i.number).join(", "));

											return (
												<TableRow key={item.id}>
													<TableCell label={ t("Card number") }>{ cardNumber }</TableCell>
													<TableCell
														label={ t("Card status") }
														type={ status === "blocked" ? "error" : "" }
													>
														{ onModeration && <Caption color="error">• { t("on moderation") }</Caption> }
														{ printCardStatus(status)}
													</TableCell>
													<TableCell label={ t("Driver") }>{driverName}</TableCell>
													<TableCell label={ t("Cars") }>{driverCars}</TableCell>
													<TableCell>
														<More actions={this.createActions(item.id, item)} />
													</TableCell>
												</TableRow>
											);
										})
									: <TableBodyEmpty message={ t("Not found cards") } />
						}
					</Table>

					<Pagination
						disabled={list.pending}
						data={list.pagination}
						urlParam="cards"
						onPageChange={this.updateList.toPage} />
				</div>

				<CardsMessages
					confirmChangeStatus={this.blockCardHandler}
				/>
			</View>
		);
	}

	getCardSearchParam = () => cardsStore.list.getParam(SEARCH_QUERY_PARAM);
	updateList = createListUpdater(cardsStore.list, this.props.history, this.props.location);
	updateSearch = this.updateList.toQuery(SEARCH_QUERY_PARAM, this.getCardSearchParam());

	updateDataBySearch = async (search: string) => {
		const params = QueryController.getParamsFromSearch(search, []);
		await cardsStore.list.updateData({
			...params,
			...(params[SEARCH_QUERY_PARAM] && ({ [SEARCH_QUERY_PARAM]: this.getCardSearchParam()}))
		});
	};

	blockCardHandler = () => {
		cardsStore.staff.changeStatus("blocked", () => {

			if(!cardsStore.staff.actionPayload) {
				return false;
			}
			const { id } = cardsStore.staff.actionPayload;
			const item = cardsStore.list.getItemById(id);

			item.updateValue({ status: "blocked" });

			if(cardsStore.list.params.status === "active") {
				cardsStore.list.filter((item: any) => item.id !== id)
					.then(() => {});
			}
		});
	};

	validateActionsByDeps = (depsOut: boolean[]): boolean => {
		if(typeof depsOut === "undefined" || !Array.isArray(depsOut) || depsOut.length === 0) {
			return true;
		}

		/**
		 * onModeration: true => must be avoided;
		 * isBlocked: true => must be avoided;
		 */

		return !depsOut.some(dependency => dependency);
	};

	createActions = (id: string, item: IItemController<TCardItem>) => {
		logger(item.id, { item: item.value });
		const { onModeration, status } = item.value;
		const isBlocked: boolean = status === "blocked";

		return [
			{
				icon: "edit",
				depsOut: [isBlocked],
				title: this.props.t("Edit card"),
				handler: () => this.props.history.push("/cards/edit/" + id)
			},
			{
				icon: "reorder",
				depsOut: [],
				title: this.props.t("Status of limits"),
				handler: () => this.props.history.push("/cards/limits/" + id)
			},
			{
				icon: "lock",
				depsOut: [onModeration, isBlocked],
				title: this.props.t("Block card"),
				handler: () => cardsStore.staff.requestAction(STAFF_ACTION_CHANGE_STATUS, id, item)
			}
		].filter((action: any) => this.validateActionsByDeps(action.depsOut));
	};
}

export default withTranslation()(withRouter(observer(CardsList)));
