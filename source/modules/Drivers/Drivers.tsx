import React from "react";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router";
import DriverCreate from "./DriverCreate";
import DriverEdit from "./DriverEdit";
import DriversList from "./DriversList";
import {TRouteParams} from "@app-types/TRouteParams";
import Can from "../../components/Can";

const Drivers = ({ match }: RouteComponentProps) => {
	const { id = "" }: TRouteParams = match.params;
	return (
		<Switch>
			<Route exact={true} path={"/drivers"}>
				<Can perform="drivers:list">
					<DriversList />
				</Can>
			</Route>

			<Route exact={true} path={"/drivers/create"}>
				<Can perform="drivers:create">
					<DriverCreate />
				</Can>
			</Route>

			<Route exact={true} path={"/drivers/edit/:id"}>
				<Can perform="drivers:edit">
					<DriverEdit id={id} />
				</Can>
			</Route>

			<Redirect to="/drivers" />
		</Switch>
	);
};

export default Drivers;
