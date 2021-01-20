import React from "react";
import { TAccessRole } from "@app-types/TAccessRole";
import Home from "../modules/Home";
import Company from "../modules/Company";
import Users from "../modules/Users";
import Cards from "../modules/Cards";
import Drivers from "../modules/Drivers";
import Documents from "../modules/Documents";
import Tickets from "../modules/Tickets";
import Feedback from "../modules/Feedback";
import Notifications from "../modules/Notifications";
import Transactions from "../modules/Transactions";
import {TIconType} from "@app-types/TIconType";
import UserMe from "../modules/Users/UserMe";
import {TFunction} from "i18next";


export type TNavGroup = {
	id: string,
	title: string
}

export type TNavSection = {
	id: string,
	title: string,
	buttons: TRouteConfig[]
}

export type TRouteConfig = {
	name: string,
	title: string,
	component: React.ComponentType<any>,
	badge?: React.ComponentType<any> | null,
	icon?: TIconType,
	group: string | null,
	entry: string,
	depsEntry?: string[],
	path: string,
	roles: TAccessRole[]
}

export const ROLE_ADMIN: TAccessRole = "admin";
export const ROLE_MANAGER: TAccessRole = "manager";
export const ROLE_ACCOUNTANT: TAccessRole = "accountant";
export const ROLES_ALL: TAccessRole[] = [ ROLE_ACCOUNTANT, ROLE_MANAGER, ROLE_ADMIN ];

export const NAV_GROUP_COMMON = "common";
export const NAV_GROUP_SETTINGS = "settings";

export const groups = (t: TFunction): TNavGroup[] => {
	const translate = t ? t : (k: string) => k;

	return [
		{
			id: NAV_GROUP_COMMON,
			title: translate("Control Panel")
		},
		{
			id: NAV_GROUP_SETTINGS,
			title: ""
		}
	];
};


const routes = (t?: TFunction): TRouteConfig[] => {
	const translate = t ? t : (k: string) => k;

	return [
		{
			name: "home",
			title: translate("Main page"),
			component: Home,
			badge: null,
			entry: "/",
			path: "/",
			icon: "home",
			roles: ROLES_ALL,
			group: NAV_GROUP_COMMON
		},
		{
			name: "company",
			title: translate("Company"),
			component: Company,
			badge: null,
			icon: "cog",
			entry: "/company",
			depsEntry: ["/users", "/drivers"],
			path: "/company/:action?",
			roles: [ROLE_ADMIN],
			group: NAV_GROUP_SETTINGS
		},
		{
			name: "users",
			title: translate("Users"),
			component: Users,
			badge: null,
			entry: "/users",
			path: "/users/:action?/:id?",
			roles: [ROLE_ADMIN],
			group: null
		},
		{
			name: "me",
			title: translate("My Profile"),
			component: UserMe,
			badge: null,
			icon: "profile",
			entry: "/me",
			path: "/me",
			roles: ROLES_ALL,
			group: NAV_GROUP_SETTINGS
		},
		{
			name: "cards",
			title: translate("Fuels cards"),
			component: Cards,
			badge: null,
			icon: "card",
			entry: "/cards",
			path: "/cards/:action?/:id?",
			roles: ROLES_ALL,
			group: NAV_GROUP_COMMON
		},
		{
			name: "drivers",
			title: translate("Drivers"),
			component: Drivers,
			badge: null,
			entry: "/drivers",
			path: "/drivers/:action?/:id?",
			roles: ROLES_ALL,
			group: null
		},
		{
			name: "documents",
			title: translate("Documents"),
			component: Documents,
			badge: null,
			icon: "doc",
			entry: "/documents",
			path: "/documents/:action?/:id?",
			roles: [ ROLE_ADMIN, ROLE_ACCOUNTANT ],
			group: NAV_GROUP_COMMON
		},
		{
			name: "tickets",
			title: translate("Online tickets"),
			component: Tickets,
			badge: null,
			icon: "ticket",
			entry: "/tickets",
			path: "/tickets/:action?/:id?",
			roles: ROLES_ALL,
			group: null
		},
		{
			name: "feedback",
			title: translate("Feedback"),
			component: Feedback,
			badge: null,
			icon: "comment",
			entry: "/feedback",
			path: "/feedback",
			roles: ROLES_ALL,
			group: NAV_GROUP_SETTINGS
		},
		{
			name: "notifications",
			title: translate("Notifications"),
			component: Notifications,
			badge: null,
			entry: "/notifications",
			path: "/notifications",
			roles: ROLES_ALL,
			group: null
		},
		{
			name: "transactions",
			title: translate("Transactions history"),
			component: Transactions,
			badge: null,
			icon: "reorder",
			entry: "/transactions",
			path: "/transactions/:action?",
			roles: ROLES_ALL,
			group: NAV_GROUP_COMMON
		}
	];
};

export { routes };
