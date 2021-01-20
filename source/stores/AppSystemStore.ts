import {decorate, action, observable, runInAction} from "mobx";
import {get} from "../libs";

const TIMEOUT = 2.5 * (60 * 1000);

class AppSystemStore {
	lastSystemUpdate: Date | null = null;
	lastSystemUpdatePending: boolean = false;

	fetchLastSystemUpdate = (onSuccess: (data: { dateTime: Date }) => void) => {
		get<{ dateTime: Date }>({ endpoint: "/last-system-update"})
			.then((res) =>{
				runInAction(() => {
					onSuccess(res.data);
				});
			})
			.catch(() => {});
	};

	initialFetchLastSystemUpdate = () => {
		this.lastSystemUpdatePending = true;

		this.fetchLastSystemUpdate((data) => {
			this.lastSystemUpdate = data.dateTime;
			this.lastSystemUpdatePending = false;
			setTimeout(this.quietFetchLastSystemUpdate, TIMEOUT);
		});
	};

	private quietFetchLastSystemUpdate = () => {
		this.fetchLastSystemUpdate((data) => {
			this.lastSystemUpdate = data.dateTime;
		});
		setTimeout(this.quietFetchLastSystemUpdate, TIMEOUT);
	}
}

decorate(AppSystemStore, {
	lastSystemUpdate: observable,
	lastSystemUpdatePending: observable,
	initialFetchLastSystemUpdate: action
});

const appSystemStore = new AppSystemStore();

export default appSystemStore;
