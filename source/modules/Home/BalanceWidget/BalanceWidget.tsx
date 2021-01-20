import React from "react";
import "./styles.scss";
import Text, {Caption, Label, Paragraph} from "../../../ui/Typography";
import {formatDate, printFormattedSum, propOf} from "../../../libs";
import Button from "../../../ui/Button";
import DiscountHistory from "../DiscountHistory";
import Tooltip from "../../../ui/Tooltip";
import Can from "../../../components/Can";
import {observer} from "mobx-react";
import homeStore from "../homeStore";
import {WithTranslation, withTranslation} from "react-i18next";

type Props = { } & WithTranslation

type LabelValueProps = {
	label: string,
	value: string,
	isImportant?: boolean
}


const LabelValue = ({ label, value, isImportant }: LabelValueProps) => {
	return (
		<div className="c-balance-widget__row">
			<Label>{label}</Label>
			<Text type="paragraph" bold={isImportant}>{value}</Text>
		</div>
	);
};

const BalanceWidget = withTranslation()(observer(({t}: Props) => {

	const { setShowHistory, showHistory, dashboardInfo, dashboardInfoReady } = homeStore;


	const currentBalance = dashboardInfoReady
		? propOf<any>(dashboardInfo, "balance", "N/A", (d) => `${d.sign}${printFormattedSum(d.value)}`)
		: "--";

	const availableBalance = dashboardInfoReady
		? propOf<any>(dashboardInfo, "availableBalance", "N/A", printFormattedSum)
		: "--";

	const creditLimit = dashboardInfoReady
		? propOf<any>(dashboardInfo, "creditLimit", "N/A", printFormattedSum)
		: "--";

	const lastUpdatedAt = dashboardInfoReady
		? propOf<any>(dashboardInfo, "balanceUpdate.dateTime", "N/A", (val) => formatDate({date: val}))
		: "--";

	const lastUpdatedBalance = dashboardInfoReady
		? propOf<any>(dashboardInfo, "balanceUpdate.balance", "N/A", (val) => `${val.sign}${printFormattedSum(val.value)}`)
		: "--";

	const lastMonthDiscountSum = dashboardInfoReady
		? propOf<any>(dashboardInfo, "lastMonthDiscountSum", "N/A", printFormattedSum)
		: "--";

	return (
		<div className="c-balance-widget">

			<div className="c-balance-widget__current">

				<LabelValue label={t("Current balance")} value={currentBalance} isImportant />

				<div className="c-balance-widget__update">
					<Caption className="c-balance-widget__update-label">{ t("Time and date of balance update") }</Caption>
					<div className="c-balance-widget__update-info">
						<Caption>
							{ lastUpdatedBalance } <br />
							{ lastUpdatedAt }
						</Caption>
						<Tooltip size="medium" message={ t("Date of last refill")} tooltipKey="balance-last-update" />
					</div>
				</div>

			</div>
			<div className="c-balance-widget__limits">
				<LabelValue label={t("Credit limit")} value={creditLimit} />
				<LabelValue label={t("Available")} value={availableBalance} />
			</div>
			<div className="c-balance-widget__history">
				<div className="c-balance-widget__history-col">
					<Paragraph className="c-balance-widget__history-sum">{lastMonthDiscountSum}</Paragraph>
					<Text type="link">{ t("Sum discount for current month") }</Text>
				</div>

				<div className="c-balance-widget__history-col">
					<Button
						type="ghost"
						icon="chevron-right"
						direction="reverse"
						to="/transactions"
						onClick={() => setShowHistory(true)}
					>
						{ t("History of charges") }
					</Button>
					<Can perform="dev:component" use={null}>
						<Text type="note" to="/loyalty-program" underline>{ t("Current discount rules") }</Text>
					</Can>
				</div>
			</div>

			<div className="c-balance-widget__actions">

				<Can perform="documents:list" use={null}>
					<Button type="primary" to="/documents/bill">{ t("Refill balance") }</Button>
				</Can>

				<Can perform="home:bonuses" use={null}>
					<Button type="alt" href={process.env.SHELL_LINK_GET_BONUSES}>{ t("Get bonuses") }</Button>
				</Can>

			</div>
			{ showHistory && <DiscountHistory onClose={() =>  setShowHistory(false)}/> }
		</div>
	);
}));

export default BalanceWidget;
