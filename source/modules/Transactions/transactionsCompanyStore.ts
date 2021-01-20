import {observable, decorate, computed, toJS} from "mobx";
import ListStore from "../../stores/ListStore";
import {TTransactionType} from "../../config/dictionary";
import FiltersStore from "../../stores/FiltersStore";
import ReportStore from "../../stores/ReportStore";
import {format} from "date-fns";

type TTransactionItem = {
	amount: number
	type: keyof TTransactionType
	createdAt: Date
}

class TransactionsCompanyStore {
	list = new ListStore<TTransactionItem>("/transactions/company", {
		page: "1",
		tab: "company", // "company" | "cards"
		sort: "createdAt",
		order: "desc"
	});

	filters = new FiltersStore();

	reportService = new ReportStore(
		"/transactions/company/report",
		() => this.filters.getApplied(),
		() => {
			const dateFormatString: string = "dd-MM-yyyy_HH-mm-ss";
			const filenamePrefix: string = window.__app__static__i18n__.companyTransactionFileNamePrefix;
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

decorate(TransactionsCompanyStore, {
	list: observable,
	accountBalance: computed,
	filteredData: computed,
});

const transactionsCompanyStore = new TransactionsCompanyStore();

export default transactionsCompanyStore;
