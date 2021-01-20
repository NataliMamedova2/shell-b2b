import {decorate, action, observable, runInAction, computed} from "mobx";
import {get} from "../../libs";

type TBalance = { value: number, sign: string } | null

export type TCardsStats = { day: number, week: number, month: number } | null

type TDashboardInfo = {
	balance: TBalance,
	balanceUpdate: {
		balance: TBalance,
		dateTime: Date
	} | null,
	creditLimit: number,
	availableBalance: number,
	lastMonthDiscountSum: number
	cardsStatistic: TCardsStats
}

class HomeStore {

	dashboardInfo: TDashboardInfo | null = null;
	dashboardInfoPending: boolean = false;
	fetchDashboardInfo = () => {
		this.dashboardInfoPending = true;

		get<TDashboardInfo>({ endpoint: "/dashboard"})
			.then(res => {
				runInAction(() => {
					this.dashboardInfo = res.data;
					this.dashboardInfoPending = false;
				});
			});
	};

	showHistory: boolean = false;
	setShowHistory = (val: boolean) => ( this.showHistory = val );

	get dashboardInfoReady(): boolean {
		return !this.dashboardInfoPending && !!this.dashboardInfo && Object.keys(this.dashboardInfo).length > 0;
	}
}

decorate(HomeStore, {
	dashboardInfo: observable,
	dashboardInfoPending: observable,
	fetchDashboardInfo: action,
	dashboardInfoReady: computed,

	showHistory: observable,
	setShowHistory: action
});

const homeStore = new HomeStore();

export default homeStore;
