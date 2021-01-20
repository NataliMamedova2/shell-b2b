import React, {Component, Fragment} from "react";
import "./styles.scss";
import View from "../../../components/View";
import Back from "../../../ui/Back";
import {Tabs, TabsItem} from "../../../ui/Tabs";
import {
	Table,
	TableBodyEmpty,
	TableCell,
	TableHead,
	TableRow,
	TableRowPlaceholder,
	TableSortCell
} from "../../../ui/Table";
import {Caption, H2, Label, Note, Paragraph} from "../../../ui/Typography";
import {normalizeAmount, printFormattedSum, propOf} from "../../../libs";
import Paper from "../../../ui/Paper";
import {useTranslation, withTranslation, WithTranslation} from "react-i18next";
import cardsStore, {TCardLimitsItem, TLimitsData} from "../cardsStore";
import { withRouter, RouteComponentProps } from "react-router-dom";
import {observer} from "mobx-react";
import {PENDING_VALUE_CARD_NUMBER} from "../../../config/constants";
import {printCardStatus, printShortDayOfWeek} from "../../../config/dictionary";
import PageTitle from "../../../components/PageTitle";
import QueryController from "../../../stores/ListStore/QueryController";
import {createListUpdater} from "../../../stores/ListStore/createListUpdater";
import {IItemController} from "../../../stores/ListStore/ItemController";
import {TSearchParams} from "../../../stores/ListStore/ListStore";

type Props = {
	id: string
} & WithTranslation & RouteComponentProps

type WithNull<T> = T | null;
type TMoneyLimitsProp = WithNull<TLimitsData["moneyLimits"]>;
type TCardProp = WithNull<TLimitsData["card"]>;

class CardLimits extends Component<Props> {
	list = cardsStore.createLimitsStore(this.props.id);

	async componentDidMount() {
		await this.updateData();
	}
	async componentDidUpdate(prevProps: any, prevState: any) {
		if(this.props.location.search !== prevProps.location.search) {
			await this.updateData();
		}
	}

	render() {
		const { t, id } = this.props;
		const card = propOf<TCardProp>(this.list.metaInfo, "card", null);
		const moneyLimits = propOf<TMoneyLimitsProp>(this.list.metaInfo, "moneyLimits", null);
		const cardNumberForTitle = propOf<string>(card, "cardNumber", "", (num: any) => ` - #${num}`);

		const noGoodsLimits = !this.list.pending && propOf<any[]>(card, "goodsLimits", []).length === 0;
		const noServicesLimits = !this.list.pending && propOf<any[]>(card, "servicesLimits", []).length === 0;

		return (
			<View className="m-cards-limits">
				<PageTitle contentString={`${t("Fuel cards limits")}${cardNumberForTitle}`} />
				<div className="m-cards-limits__header">
					{
						propOf<any, boolean>(card, "status", false, (value: string) => value !== "blocked")
						? <Back to={"/cards/edit/" + id} />
						: <Back to={ this.list.pending ? undefined : "/cards"} />
					}
					<div className="m-cards-limits__info">
						<div className="m-cards-limits__col">
							<div className="m-cards-limits__label m-cards-limits__label--status">
								<Label>{t("Fuel card")}</Label>
								{
									propOf<boolean>(card, "onModeration", false)
										? <Paragraph className="m-cards-limits__status" color="error">• { t("on moderation") }</Paragraph>
										: <Paragraph className="m-cards-limits__status" color="error">
											{ propOf<string>(card, "status", "...", (s: any) => `• ${printCardStatus(s)}`) }
										</Paragraph>
								}
							</div>
							<H2 className="m-cards-limits__card-number">
								{ propOf<string>(card, "cardNumber", PENDING_VALUE_CARD_NUMBER) }
							</H2>
						</div>

						<div className="m-cards-limits__col">
							<div className="m-cards-limits__label">
								<Label>{t("work days")}</Label>
							</div>
							<Paragraph>
								{ propOf<any, string>(card, "serviceDays", "--", (s: any[]) => s.map(printShortDayOfWeek).join(", ")) }
							</Paragraph>
						</div>

						<div className="m-cards-limits__col">
							<div className="m-cards-limits__label">
								<Label>{t("work hours")}</Label>
							</div>
							<Paragraph>
								{ propOf<string>(card, "startUseTime", "--:--") }
								{" - "}
								{ propOf<string>(card, "endUseTime", "--:--") }
							</Paragraph>
						</div>
					</div>
				</div>

				<div className="m-cards-limits__body">
					<Paper className="m-cards-limits__paper">
						<Tabs
							pending={this.list.pending}
							type="auto"
							defaultValue="fuel"
							activeValue={this.list.getParam("type")}
							onChange={this.updateList.toTab("type")}
						>
							<TabsItem value="fuel">{ t("Fuel") }</TabsItem>
							<TabsItem disabled={noGoodsLimits} value="goods">{ t("Goods") }</TabsItem>
							<TabsItem disabled={noServicesLimits} value="service">{ t("Services") }</TabsItem>
						</Tabs>
					</Paper>
					<Table withMore grid={[ 200, [3,1] ]}>
						<TableHead>
							<TableSortCell>{ t("Limit") }</TableSortCell>
							<TableSortCell>{ t("Per day") }</TableSortCell>
							<TableSortCell>{ t("Per week") }</TableSortCell>
							<TableSortCell>{ t("Per month") }</TableSortCell>
						</TableHead>

						<MoneyLimitsList moneyLimits={moneyLimits}/>
						<CustomLimitsList pending={this.list.pending} items={this.list.items} />
					</Table>

					<div className="m-cards-limits__footer" />
				</div>

			</View>
		);
	}

	updateData = async () => {
		const validTypes = ["goods", "fuel", "service"];
		const defaultParams: TSearchParams = { type: "fuel" };
		const params = QueryController.getParamsFromSearch(this.props.location.search, []);

		if(validTypes.includes(params.type as string) || typeof params.type === "undefined") {
			await this.list.updateData(params);
		} else {
			await this.list.updateData(defaultParams);

			this.props.history.push({
				pathname: this.props.location.pathname,
				search: "?" + this.list.getUrlSearchParams()
			});

		}
	};

	updateList = createListUpdater(this.list, this.props.history, this.props.location);
}

function CustomLimitsList ({ pending, items, ...props}: { pending: boolean, items: IItemController<TCardLimitsItem>[] }) {
	const { t } = useTranslation();
	if(pending) {
		return <TableRowPlaceholder count={4} />;
	}

	if(items.length === 0) {
		return <TableBodyEmpty message={ t("No limits") }/>;
	}

	return (
		<Fragment>
			{
				items.map((item) => {

					const { name, day, week, month } = item.value;
					return (
						<TableRow key={item.id} {...props}>
							<TableCell label={ t("Limit") }>{ name }</TableCell>
							<TableCell label={ t("Per day") }>
								{ normalizeAmount(day.total) }
								<Note color="darkgrey">{t("left")} {normalizeAmount(day.left)}</Note>
							</TableCell>
							<TableCell label={ t("Per week") }>
								{ normalizeAmount(week.total) }
								<Note color="darkgrey">{t("left")} {normalizeAmount(week.left)}</Note>
							</TableCell>
							<TableCell label={ t("Per month") }>
								{ normalizeAmount(month.total) }
								<Note color="darkgrey">{t("left")} {normalizeAmount(month.left)}</Note>
							</TableCell>
						</TableRow>
					);
				})
			}
		</Fragment>
	);
}

function MoneyLimitsList({ moneyLimits, ...props }: { moneyLimits: TMoneyLimitsProp }) {
	const { t } = useTranslation();

	if(!moneyLimits) {
		return null;
	}

	return (
		<TableRow {...props}>
			<TableCell label={ t("Limit") }>{ moneyLimits.name }</TableCell>
			<TableCell label={ t("Per day") }>
				{ printFormattedSum(moneyLimits.day.total) }
				<Note color="darkgrey">{t("left")} { printFormattedSum(moneyLimits.day.left) }</Note>
			</TableCell>
			<TableCell label={ t("Per week") }>
				{ printFormattedSum(moneyLimits.week.total) }
				<Note color="darkgrey">{t("left")} { printFormattedSum(moneyLimits.week.left) }</Note>
			</TableCell>
			<TableCell label={ t("Per month") }>
				{ printFormattedSum(moneyLimits.month.total) }
				<Note color="darkgrey">{t("left")} { printFormattedSum(moneyLimits.month.left) }</Note>
			</TableCell>
		</TableRow>
	);
}

export default withTranslation()(withRouter(observer(CardLimits)));
