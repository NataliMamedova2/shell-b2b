import React from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import NotFoundError from "./NotFoundError";
import AccessDeniedError from "./AccessDeniedError";

const Error = () => {
	return (
		<Switch>
			<Route exac path="/error/not-found">
				<NotFoundError />
			</Route>

			<Route exact path="/error/access-denied">
				<AccessDeniedError />
			</Route>

			<Redirect to="/error/not-found" />
		</Switch>
	);
};

export default Error;
