import React, {useEffect} from "react";
import "./styles.scss";
import View from "../../../components/View";
import { Label } from "../../../ui/Typography";
import Button from "../../../ui/Button";
import PageHeader from "../../../components/PageHeader";
import Badge from "../../../ui/Badge";
import Can from "../../../components/Can";
import {useTranslation} from "react-i18next";
import {observer} from "mobx-react";
import companyStore, {TDashboardInfo} from "../companyStore";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import profileStore from "../../Users/profileStore";
import PageTitle from "../../../components/PageTitle";

const ICON_URL_USER: string = "/media/user.svg";

/** Use these icon for the `SMS` and `Loyalty` blocks. When you're gonna to implement they */
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const ICON_URL_HANDS: string = "/media/hands.svg";
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const ICON_URL_PHONE: string = "/media/phone.svg";

type TDashboardBadgeProps = { property: keyof TDashboardInfo}

const ObserverDashboardBadge = observer(({ property }: TDashboardBadgeProps) => {
	const getPropertyValue = () => property in companyStore.dashboardInfo ? companyStore.dashboardInfo[property] : "-";
	return (
		<Badge type="alt">
			{ companyStore.fetchPending ? <PendingIcon/> : getPropertyValue() }
		</Badge>
	);
});


const CompanyDashboard = () => {
	const { t } = useTranslation();

	useEffect(() => {

		(async () => await companyStore.fetchInfo())();

	}, []);

	return (
		<View className="m-company-dashboard">
			<PageTitle contentString={ `${t("Company")} - ${profileStore.myCompany.name}` } />
			<PageHeader title={ profileStore.myCompany.name }>

				<Can perform="company:edit" use={null}>
					<Button type="alt" to="/company/edit">{ t("Edit general information") }</Button>
				</Can>

			</PageHeader>
			
			<div className="c-company-grid">

				<Can perform="users:list" use={null}>
					<div className="c-company-grid__item">
						<div className="c-company-grid__head">
							<Label>{ t("Active users") }</Label>
							<ObserverDashboardBadge property="usersCount" />
						</div>
						<div className="c-company-grid__actions">
							<Button type="alt" to="/users">{ t("Edit users") }</Button>
						</div>
					</div>
				</Can>


				<Can perform="drivers:list" use={null}>
					<div className="c-company-grid__item">
						<img className="c-company-grid__icon" src={ICON_URL_USER} alt=" "/>
						<div className="c-company-grid__head">
							<Label>{ t("Drivers") }</Label>
							<ObserverDashboardBadge property="driversCount" />
						</div>
						<div className="c-company-grid__actions">
							<Button type="alt" to="/drivers">{ t("Settings") }</Button>
						</div>
					</div>
				</Can>

			</div>
		</View>
	);
};

export default CompanyDashboard;
