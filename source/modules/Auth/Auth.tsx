import React from "react";
import {Redirect, Route, Switch} from "react-router";
import {BrowserRouter as Router} from "react-router-dom";

import SignIn from "./SignIn";
import RecoveryPasswordMessage from "./RecoveryPasswordMessage";
import RecoveryPassword from "./RecoveryPassword";
import "./styles.scss";
import i18n from "../../config/i18n";
import {I18nextProvider} from "react-i18next";
import {Suspense} from "react";
import Loader from "../../components/Loader";
import {getCurrentLanguage} from "../../config/i18n/getCurrentLanguage";

const Auth = () => {
	const currentLanguage = getCurrentLanguage();

	return (
		<Router basename={`/${currentLanguage}`}>
			<I18nextProvider i18n={i18n}>
				<Suspense fallback={ <Loader message={window.__app__static__i18n__.loadingTranslations} key="i18n_cabinet" /> }>
					<Switch>
						<Route exact path="/auth">
							<SignIn language={currentLanguage} />
						</Route>
						<Route exact path="/auth/restore">
							<RecoveryPassword />
						</Route>
						<Route exact path="/auth/restore-message">
							<RecoveryPasswordMessage />
						</Route>
						<Redirect to="/auth"/>
					</Switch>
				</Suspense>
			</I18nextProvider>
		</Router>
	);
};

export default Auth;
