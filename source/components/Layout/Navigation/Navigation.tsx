import React from "react";
import "./styles.scss";
import config from "../../../config";
import { generateNavigation } from "../../../libs";
import { Label, Caption} from "../../../ui/Typography";
import {TNavSection, TRouteConfig} from "../../../config/routes";
import { Link } from "react-router-dom";
import Icon from "../../../ui/Icon";
import appAuthStore from "../../../stores/AppAuthStore";
import {useTranslation} from "react-i18next";
import appUIStore from "../../../stores/AppUIStore";
import profileStore from "../../../modules/Users/profileStore";
import { useLocation } from "react-router-dom";
import classNames from "classnames";

type SectionProps = {
	section: TNavSection
}

type ItemProps = {
	button: TRouteConfig
}

const routeIsActive = (pathname: string, entry: string, depsEntry?: string[]): boolean => {
	const pathnameEntry = "/" + pathname.split("/")[1]; //  `/page`

	if(depsEntry && depsEntry.some(item => pathnameEntry === item)) {
		return true;
	}

	return pathnameEntry === entry;
};

const NavigationItem = ({ button }: ItemProps) => {
	const { pathname } = useLocation();

	const classes = classNames("m-navigation__item", {
		"is-active": routeIsActive(pathname, button.entry, button.depsEntry)
	});
	return (
		<Link className={classes} to={button.entry} key={button.name} onClick={appUIStore.toggleNav(false)}>
			{ button.icon ? <Icon type={button.icon} /> : null }
			<Caption>{ button.title }</Caption>
			{ button.badge ? <button.badge /> : null}
		</Link>
	);
};

const NavigationSection = ({ section }: SectionProps) => {
	const { title, buttons } = section;

	return (
		<div  className="m-navigation__section">
			{ title && <Label className="m-navigation__title">{ title }</Label> }
			{ buttons.map(button => <NavigationItem key={button.name} button={button} />) }
		</div>
	);
};

const Navigation = () => {
	const { t } = useTranslation();
	const routes = config.routes(t);
	const sections = generateNavigation(routes, profileStore.userRole, t);

	return (
		<div className="m-navigation">
			{
				sections.map(section => <NavigationSection section={section} key={section.id}/>)
			}
			<div className="m-navigation__section">
				<div className="m-navigation__item" onClick={appAuthStore.logOut} role="button">
					<Icon type="logout" />
					<Caption>{ t("Sign out") }</Caption>
				</div>
			</div>
		</div>
	);
};

export default Navigation;
