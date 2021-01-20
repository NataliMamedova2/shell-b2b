import React from "react";
import CompanyEdit from "./CompanyEdit";
import CompanyDashboard from "./CompanyDashboard";
import CompanyLoyaltyProgram from "./CompanyLoyaltyProgram";
import { Route, Switch, Redirect } from "react-router-dom";
import Can from "../../components/Can";

const Company = () => {
	return (
		<Switch>
			<Route exact={true} path={"/company"}>
				<Can perform="company:main">
					<CompanyDashboard />
				</Can>
			</Route>
			<Route exact={true} path={"/company/edit"}>
				<Can perform="company:edit">
					<CompanyEdit />
				</Can>
			</Route>

			<Route exact={true} path={"/company/loyalty-program"}>
				<Can perform="company:loyalty">
					<CompanyLoyaltyProgram />
				</Can>
			</Route>
			<Redirect to="/company" />
		</Switch>
	);
};

export default Company;
