import React from "react";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router";
import TicketsCreate from "./TicketsCreate";
import TicketsEdit from "./TicketsEdit";
import TicketsList from "./TicketsList";
import {TRouteParams} from "@app-types/TRouteParams";
import Can from "../../components/Can";

const Tickets = ({...props}: RouteComponentProps) => {

	const { id } : TRouteParams = props.match.params;

	return (
		<Switch>
			<Route exact={true} path={"/tickets"}>
				<Can perform="tickets:list">
					<TicketsList {...props} />
				</Can>
			</Route>

			<Route exact={true} path={"/tickets/create"}>
				<Can perform="tickets:create">
					<TicketsCreate />
				</Can>
			</Route>

			<Route exact={true} path={"/tickets/edit/:id"}>
				<Can perform="tickets:edit">
					<TicketsEdit id={id} />
				</Can>
			</Route>

			<Redirect to="/tickets" />
		</Switch>
	);
};

export default Tickets;
