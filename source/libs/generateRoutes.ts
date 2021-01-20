import {TRouteConfig, groups, TNavGroup, TNavSection } from "../config/routes";
import { TAccessRole } from "@app-types/TAccessRole";
import {TFunction} from "i18next";


const filterRoutesByRole = (routes: TRouteConfig[], role: TAccessRole): TRouteConfig[] => {
	if (typeof role === "undefined") {
		return routes;
	}
	return routes.filter(route => route.roles.indexOf(role) !== -1);
};

const generateNavigation = (routes: TRouteConfig[], userRole: TAccessRole, t: TFunction) : TNavSection[] => {
	const filteredRoutes = filterRoutesByRole(routes, userRole);

	return groups(t).reduce((acc: TNavSection[], current: TNavGroup): TNavSection[] => {
		const { id, title } = current;
		acc.push({
			id,
			title,
			buttons: filteredRoutes.filter(item => item.group === current.id)
		});
		return acc;

	}, []);

};

const generateModules = (routes: TRouteConfig[], userRole: TAccessRole) => filterRoutesByRole(routes, userRole);

export { generateNavigation, generateModules };
