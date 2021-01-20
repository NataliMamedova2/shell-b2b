import {observable, decorate, computed, toJS} from "mobx";
import ListStore from "../../stores/ListStore";
import FiltersStore from "../../stores/FiltersStore";
import {TTransactionStatus} from "../../config/dictionary";
import ReportStore from "../../stores/ReportStore";
import {format} from "date-fns";

type TTransactionItem = {
	cardNumber: string
	fuelName: string
	volume: number
	networkStation: string
	amount: number
	price: number
	status: keyof TTransactionStatus
	createdAt: Date
}

class TransactionsCardsStore {
	list = new ListStore<TTransactionItem>("/transactions/card", {
			page: "1",
			tab: "company", // "company" | "cards"
			sort: "createdAt",
			order: "desc",
		});

	filters = new FiltersStore();

	reportService = new ReportStore(
		"/transactions/card/report",
		() => this.filters.getApplied(),
		(params) => {
			const dateFormatString: string = "dd-MM-yyyy_HH-mm-ss";
			const filenamePrefix: string = window.__app__static__i18n__.cardTransactionFileNamePrefix;
			const generatedAt = format(Date.now(), dateFormatString);
			const filenameParts = [ filenamePrefix, generatedAt].filter(Boolean).join("_");
			return filenameParts.concat(".xls");
		}
	);

	get accountBalance () {
		return this.list.metaInfo ? this.list.metaInfo.accountBalance : "--";
	}

	get filteredData () {
		return this.list.metaInfo ? toJS(this.list.metaInfo) : {};
	}
}

decorate(TransactionsCardsStore, {
	list: observable,
	accountBalance: computed,
	filteredData: computed,
});

const transactionsCardsStore = new TransactionsCardsStore();

export default transactionsCardsStore;
