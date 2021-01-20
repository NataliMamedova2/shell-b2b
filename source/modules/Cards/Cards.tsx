import React from "react";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router";
import {TRouteParams} from "@app-types/TRouteParams";
import CardLimits from "./CardLimits";
import CardEdit from "./CardEdit";
import CardsList from "./CardsList";
import CardCreateRequest from "./CardCreateRequest";
import Can from "../../components/Can";

const Cards = ({match}: RouteComponentProps) => {

	const { id = ""}: TRouteParams = match.params;

	return (
		<Switch>
			<Route exact path={"/cards"}>
				<Can perform="cards:list">
					<CardsList />
				</Can>
			</Route>

			<Route exact path={"/cards/create"}>
				<Can perform="cards:create">
					<CardCreateRequest />
				</Can>
			</Route>

			<Route exact path={"/cards/limits/:id"}>
				<Can perform="cards:limits">
					<CardLimits id={id} />
				</Can>
			</Route>

			<Route exact path={"/cards/edit/:id"}>
				<Can perform="cards:edit">
					<CardEdit id={id} />
				</Can>
			</Route>

			<Redirect to="/cards" />
		</Switch>
	);
};

export default Cards;
