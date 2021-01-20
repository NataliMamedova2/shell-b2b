import React, { useEffect } from "react";
import "./styles.scss";
import HomeNews from "./HomeNews";
import Can from "../../components/Can";
import ManagerCard from "../../components/ManagerCard";
import View from "../../components/View";
import {useBreakpoint} from "../../libs/Breakpoint";
import Paper from "../../ui/Paper";
import { H1 } from "../../ui/Typography";
import BalanceWidget from "./BalanceWidget";
import Button from "../../ui/Button";
import Tooltip from "../../ui/Tooltip";
import ErrorBoundary from "../../components/BoundaryError";
import homeStore from "./homeStore";
import {useTranslation} from "react-i18next";
import LiteStatsCardsWidget from "./LiteStatsCardsWidget";
import PageTitle from "../../components/PageTitle";

const HomeManager = React.memo(() => {
	const { state: { isTablet, acrossMobileTablet } } = useBreakpoint();

	return acrossMobileTablet
		? <ManagerCard type={ isTablet ? "widget" : "mobile"} />
		: null;
});


const Home = () => {
	const { t } = useTranslation();

	useEffect(() => {
		homeStore.fetchDashboardInfo();
	}, []);

	return (
		<Can perform="home:main">
			<View className="m-home">
				<PageTitle contentString={ t("Main page") } />
				<HomeManager/>

				<div className="m-home__widgets">
					<Paper className="m-home__widget">
						<div className="m-home__widget-header">
							<H1>{ t("Balance") }</H1>
						</div>
						<ErrorBoundary moduleName="Balance Widget">
							<BalanceWidget />
						</ErrorBoundary>
						<Button className="m-home__widget-button" type="ghost" icon="chevron-right" direction="reverse" to="/transactions/company">{t("History of company transactions")}</Button>
					</Paper>
					<Paper className="m-home__widget">
						<div className="m-home__widget-header">
							<H1>{ t("Statistic of cards") }</H1>
							<Tooltip message={t("Information for current day, week and month")} size="medium" tooltipKey="balance-stats-message" />
						</div>
						<ErrorBoundary moduleName="Card Statistic Widget">
							<LiteStatsCardsWidget />
						</ErrorBoundary>
						<Button
							className="m-home__widget-button"
							type="ghost"
							icon="chevron-right"
							direction="reverse"
							to="/transactions/cards"
						>
							{ t("History of cards transactions") }
						</Button>
					</Paper>
				</div>

				<ErrorBoundary moduleName="Company news on dashboard">
					<HomeNews />
				</ErrorBoundary>
			</View>
		</Can>
	);
};

export default Home;
