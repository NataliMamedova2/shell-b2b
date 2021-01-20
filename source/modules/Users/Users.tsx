import React from "react";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router";
import UsersList from "./UsersList";
import UserCreate from "./UserCreate";
import UserEdit from "./UserEdit";
import UserActionsHistory from "./UserActionsHistory";
import {TRouteParams} from "@app-types/TRouteParams";
import Can from "../../components/Can";
// import UserMe from "./UserMe";

const Users = ({match}: RouteComponentProps) => {
	const { id = ""} : TRouteParams = match.params;
	return (
		<Switch>
			<Route exact path={"/users"}>
				<Can perform="users:list">
					<UsersList/>
				</Can>
			</Route>

			<Route exact path={"/users/create"}>
				<Can perform="users:create">
					<UserCreate />
				</Can>

			</Route>

			<Route exact path={"/users/edit/:id"}>
				<Can perform="users:edit">
					<UserEdit id={id} />
				</Can>
			</Route>

			<Route exact path={"/users/history"}>
				<Can perform="users:actions-history">
					<UserActionsHistory />
				</Can>
			</Route>

			<Redirect to="/users" />
		</Switch>
	);
};

export default Users;
