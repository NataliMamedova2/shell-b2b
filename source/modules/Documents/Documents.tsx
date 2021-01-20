import React from "react";
import DocumentsAct from "./DocumentsAct";
import DocumentsInvoice from "./DocumentsInvoice";
import DocumentsList from "./DocumentsList";
import DocumentsForSign from "./DocumentsForSign";
import {Redirect, Route, Switch} from "react-router";
import Can from "../../components/Can";
import RequestDebtInvoice from "./RequestDebtInvoice";
import RequestSuppliersInvoice from "./RequestSuppliersInvoice";

const Documents = () => {

	return (
		<Switch>
			<Route exact={true} path={"/documents/list"}>
				<Can perform="documents:list">
					<DocumentsList />
				</Can>
			</Route>

			<Route exact={true} path={"/documents/for-sign"}>
				<Can perform="documents:list">
					<DocumentsForSign />
				</Can>
			</Route>

			<Route exact={true} path={"/documents/act"}>
				<DocumentsAct />
			</Route>

			<Route exact={true} path={"/documents/invoice"}>
				<DocumentsInvoice />
			</Route>

			<Route exact={true} path={"/documents/invoice/custom"}>
				<RequestDebtInvoice />
			</Route>

			<Route exact={true} path={"/documents/invoice/calculation"}>
				<RequestSuppliersInvoice />
			</Route>

			<Redirect to="/documents/list" />
		</Switch>
	);
};

export default Documents;
