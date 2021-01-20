import React, {useEffect, Suspense, useState} from "react";
import ReactDOM from "react-dom";
import Layout from "./components/Layout";
import {BrowserRouter as Router, Redirect, Route, Switch, useLocation} from "react-router-dom";
import config from "./config";
import { BreakpointProvider } from "./libs/Breakpoint";
import RouterPrompt from "./components/RouterPrompt";
import uuid4  from "uuid/v4";
import Error from "./modules/Error";
import i18n from "./config/i18n";
import { I18nextProvider } from "react-i18next";
import {getCurrentLanguage} from "./config/i18n/getCurrentLanguage";
import Loader from "./components/Loader";
import profileStore from "./modules/Users/profileStore";
import appUIStore from "./stores/AppUIStore";

declare global {
	interface Window {
		__app__static__i18n__: {
			loadingTranslations: string,
			loadingProfileData: string,
			pageError: string,
			redirectToPortal: string,
			offlineMessage: string,
			cardTransactionFileNamePrefix: string,
			companyTransactionFileNamePrefix: string
		}
	}
}

const getUserConfirm = (message: any, callback: any) => {
	ReactDOM.render(
		<RouterPrompt key={uuid4()} callback={callback} message={message} />,
		document.querySelector("#portal-root")
	);
};

const scrollToZero = () => window.scrollTo(0,0);
const closeNavigation = appUIStore.toggleNav(false);

const ReflectToChangeLocation = () => {
	const { pathname, search } = useLocation();

	useEffect(() => {
		scrollToZero();
		closeNavigation();
	}, [pathname, search]);

	return null;
};

const Cabinet = () => {
	const [pending, setPending] = useState(true);
	const currentLanguage = getCurrentLanguage();

	useEffect(() => {

		profileStore.readMe(() => {
			setPending(false);
		});
	}, []);

	if(pending) {
		return <Loader message={window.__app__static__i18n__.loadingProfileData} key="check_profile_data" />;
	}

	return (
		<Router getUserConfirmation={getUserConfirm} basename={`/${currentLanguage}`}>
			<ReflectToChangeLocation />
			<I18nextProvider i18n={i18n}>
				<BreakpointProvider>
					<Suspense fallback={ <Loader message={window.__app__static__i18n__.loadingTranslations} key="i18n_cabinet" /> }>
						<Layout>
							<Switch>
								{
									config.routes().map(module => {
										const { name, path, component } = module;
										return <Route exact key={name} path={path} component={component} />;
									})
								}
								<Route path="/error/:errorName?" exact>
									<Error />
								</Route>
								<Route path="/auth">
									<Redirect to="/"/>
								</Route>
								<Redirect to="/error/not-found"/>
							</Switch>
						</Layout>
					</Suspense>
				</BreakpointProvider>
			</I18nextProvider>
		</Router>
	);
};

export default Cabinet;
