import { observable, action, decorate } from "mobx";
import SystemStore from "./SystemStore";

class AppUIStore {
	isNavActive: boolean = false;
	currentOverLayer: string | null = null;
	system = new SystemStore();

	toggleNav = (bool: boolean) => () => {
		this.isNavActive = bool;
	};

	setOverLayer = (key: string | null) => {
		this.currentOverLayer = key;
	};
	resetOverLayer = () => this.setOverLayer(null);

	freezeWindow = () => {
		document.body.classList.add("is-frozen");
	};
	unfreezeWindow = () => {
		document.body.classList.remove("is-frozen");
	};
}

decorate(AppUIStore, {
	isNavActive: observable,
	toggleNav: action,
	currentOverLayer: observable,
	setOverLayer: action,
	resetOverLayer: action
});

const appUIStore = new AppUIStore();

export default appUIStore;
