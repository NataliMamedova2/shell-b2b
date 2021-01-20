import "./vendors/polyfills";
import "./vendors/modernizr.js";
import React from "react";
import ReactDOM from "react-dom";
import Cabinet from "./Cabinet";
import Auth from "./modules/Auth";
import appAuthStore from "./stores/AppAuthStore";
import {initExceptionsVendor} from "./vendors/exeptions";
import {initConnectionListeners} from "./components/ConnectionError";
import {checkSecurity} from "./libs/security";
import InsecureError from "./components/InsecureError";
import {APP_VERSION} from "./environment";

const root: HTMLElement | null = document.getElementById("root");

console.time("App started in");

async function renderApp() {
	const overlayRoot: HTMLElement | null = document.getElementById("overlay-root");
	await initExceptionsVendor();
	await initConnectionListeners(overlayRoot);

	try {
		await checkSecurity();
		const isAuth = await appAuthStore.checkAuth();

		if(isAuth) {
			ReactDOM.render(<Cabinet/>, root);
		} else {
			ReactDOM.render(<Auth/>, root);
		}

	} catch (e) {
		console.log("Run at a insecure environment");
		ReactDOM.render(<InsecureError/>, root);
	}
	console.timeEnd("App started in");
	console.log(`App version: ${APP_VERSION}`);
}


renderApp().then(() => {
	if(root) {
		root.classList.add("is-run");
	}
});
