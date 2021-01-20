import {decorate, action, observable} from "mobx";
import FormStore from "../../stores/FormStore";
import {get} from "../../libs";

export type TDashboardInfo = {
	usersCount: number,
	driversCount: number
}

class CompanyStore {
	profileForm = new FormStore("/company/profile/update", "/company/profile");
	dashboardInfo: Partial<TDashboardInfo> = {};
	activeUserCount: number | null = null;
	activeUserCountPending: boolean = false;
	fetchPending: boolean = false;

	fetchInfo = async () => {
		this.setFetchPending(true);

		const res = await get<TDashboardInfo>({ endpoint: "/company/dashboard"});

		this.setInfo(res.data);
		this.setFetchPending(false);
	};

	setInfo = (data: TDashboardInfo) => {
		this.dashboardInfo = data;
	};

	setFetchPending = (val: boolean) => (this.fetchPending = val)
}

decorate(CompanyStore, {
	dashboardInfo: observable,
	fetchPending: observable,
	fetchInfo: action,
	setInfo: action,
	setFetchPending: action
});

const companyStore = new CompanyStore();
export default companyStore;
