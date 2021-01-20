import {action, observable, decorate } from "mobx";
import FormStore from "./FormStore";
import {getCurrentLanguage} from "../config/i18n/getCurrentLanguage";

class AppAuthStore {
	token: string = "";

	checkAuth = (): Promise<boolean> => {
		const token = localStorage.getItem("__token");

		if(token) {
			this.token = token;
			return Promise.resolve(true);
		}
		return Promise.resolve(false);
	};

	loginForm = new FormStore("/oauth");
	recoveryForm = new FormStore("/password-recovery");

	logIn = this.loginForm.post;
	submitRecovery = this.recoveryForm.post;

	logOut = () => {
		const lang = getCurrentLanguage();
		localStorage.removeItem("__token");
		window.location.href = `${window.location.origin}/${lang}/auth?from=${encodeURIComponent(window.location.pathname)}`;
	};
}

decorate(AppAuthStore, {
	token: observable,
	checkAuth: action,
	loginForm: observable,
	recoveryForm: observable
});

const appAuthStore = new AppAuthStore();

export default appAuthStore;
