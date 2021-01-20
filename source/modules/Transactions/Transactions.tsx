import React, {ReactNode } from "react";
import TransactionsCompany from "./TransactionsCompany";
import TransactionsCards from "./TransactionsCards";
import Can from "../../components/Can";
import "./styles.scss";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router";

type Props = {
	children?: ReactNode
} & RouteComponentProps

const Transactions = ({children, ...props}: Props) => {
	return (
		<Switch>
			<Route exact path={"/transactions/company"}>
				<Can perform="transactions:list">
					<TransactionsCompany />
				</Can>
			</Route>

			<Route exact path={"/transactions/cards"}>
				<Can perform="transactions:list">
					<TransactionsCards />
				</Can>
			</Route>

			<Redirect to="/transactions/company" />
		</Switch>
	);
};

export default Transactions;
