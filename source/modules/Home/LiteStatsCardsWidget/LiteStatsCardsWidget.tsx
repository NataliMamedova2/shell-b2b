import React from "react";
import "./styles.scss";
import {Label, Paragraph} from "../../../ui/Typography";
import Tooltip from "../../../ui/Tooltip/Tooltip";
import {printFormattedSum, propOf} from "../../../libs";
import {observer} from "mobx-react";
import homeStore, {TCardsStats} from "../homeStore";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import {useTranslation} from "react-i18next";


const StatsCardsInfo = ({data}: {data: TCardsStats }) => {
	const { t } = useTranslation();
	return (
		<div className="c-cards-stats__data">
			<span className="c-cards-stats__col">
				<Label className="c-cards-stats__label">
					{ t("Day") }
					<Tooltip message={ t("Current day") } tooltipKey="cards-stats-day" size="small" />
				</Label>
				<Paragraph>{ propOf<any>(data, "day", "-", printFormattedSum)}</Paragraph>
			</span>
				<span className="c-cards-stats__col">
				<Label className="c-cards-stats__label">
					{ t("Week") }
					<Tooltip message={ t("Current week")} tooltipKey="cards-stats-week" size="small" />
				</Label>
				<Paragraph>{ propOf<any>(data, "week", "-", printFormattedSum)}</Paragraph>
			</span>
				<span className="c-cards-stats__col">
				<Label className="c-cards-stats__label">
					{ t("Month") }
					<Tooltip message={ t("Current month") } tooltipKey="cards-stats-month" size="small" />
				</Label>
				<Paragraph>{ propOf<any>(data, "month", "-", printFormattedSum)}</Paragraph>
			</span>
		</div>
	);
};

const StatsEmpty = () => {
	const { t } = useTranslation();
	return (
		<div className="c-cards-stats">
			<div className="c-cards-stats__empty">
				<Label>{ t("There will statistic of your cards") }</Label>
			</div>
		</div>
	);
};

const LiteStatsCardsWidget = observer(() => {
	const { dashboardInfo, dashboardInfoPending } = homeStore;

	if(dashboardInfoPending) {
		return (
			<div className="c-cards-stats__pending">
				<PendingIcon/>
			</div>
		);
	}

	if(!(dashboardInfo && dashboardInfo.cardsStatistic)) {
		return <StatsEmpty/>;
	}
	return (
		<div className="c-cards-stats">
			<div className="c-cards-stats__view">
				<StatsCardsInfo data={dashboardInfo ? dashboardInfo.cardsStatistic : null} />
			</div>
		</div>
	);
});

export default LiteStatsCardsWidget;
