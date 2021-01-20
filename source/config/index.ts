import {routes, TRouteConfig} from "./routes";
import {TFunction} from "i18next";

interface IAppConfig {
	routes: (t?: TFunction) => TRouteConfig[],
	currency: string
}

const config: IAppConfig = {
	routes,
	currency: "uah",
};

export default config;
