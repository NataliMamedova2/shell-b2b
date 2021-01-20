import { observable, computed, decorate } from "mobx";
import ListStore from "../../stores/ListStore";
import StaffStore from "../../stores/StaffStore";
import FormStore from "../../stores/FormStore";
import {logger} from "../../libs";
import {IListStore, TListData} from "../../stores/ListStore/ListStore";
import {TCardStatus} from "../../config/dictionary";
import {TItemStatus} from "@app-types/TItemStatus";

export type TCardItem = {
	cardNumber: string
	onModeration: boolean
	status: keyof TCardStatus
}

export type TCardLimitsItem = {
	name: string
	day: { total: number, left: number }
	week: { total: number, left: number }
	month: { total: number, left: number }
}

type TCardInfo = {
	cardNumber: string,
	id: string,
	onModeration: boolean,
	serviceDays: string[],
	status: TItemStatus,
	timeUse: { start: string, end: string },
	totalLimits: { day: number, week: number, month: number }
}

export type TLimitsData = {
	card: TCardInfo,
	limits: TCardLimitsItem[],
	moneyLimits: TCardLimitsItem
}

class CardsStore {
	list = new ListStore<TCardItem>("/fuel-cards",{
		page: "1",
		status: "all", // "active" | "blocked"
		sort: "cardNumber",
		order: "desc"
	});

	staff = new StaffStore({
		read: "/fuel-cards",
		create: "/fuel-cards/order-new-card",
		update: "/fuel-cards/update",
		delete: "",
		changeStatus: (id) => `/fuel-cards/stop-list/${id}/add`
	});

	orderForm = new FormStore("/fuel-cards/order-new-card");

	createLimitsStore = (id: string): IListStore<TCardLimitsItem> => {
		logger("create limits Store", { id });

		return new ListStore<TCardLimitsItem>(
			`/fuel-cards/${id}/limits`,
			{
			type: "fuel",
			page: "1",
			cardNumber: ""
			},
			(resoledData: TLimitsData): TListData<TCardLimitsItem> => {
				return {
					meta: {
						pagination: {
							currentPage: 1,
							totalCount: 1
						},
						card: resoledData.card,
						moneyLimits: resoledData.moneyLimits,
					},
					data: resoledData.limits
				};
			}
			);
	};

	get activeCardsCount() {
		return this.list.metaInfo ? this.list.metaInfo.activeCount : 0;
	}
}

decorate(CardsStore, {
	list: observable,
	staff: observable,
	activeCardsCount: computed,
});

const cardsStore = new CardsStore();

export default cardsStore;
