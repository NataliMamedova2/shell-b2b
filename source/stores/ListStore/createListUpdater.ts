import {IListStore} from "./ListStore";
import {logger} from "../../libs";

type TUpdateMethods = {
	toPage: (selected: number) => void,
	toSort: (value: string) => void,
	toOrderedSort: (value: string) => void,
	toTab: (tabKey: string) => (value: string) => void,
	toQuery: (queryKey: string, validator?: (v: string) => string) => (value: string) => void,
	toFilter: (getFilters: () => ({[filterKey: string]: any })) => void
}

function createListUpdater<T = any>(listInstance: IListStore<T>, history: any, location: any ): TUpdateMethods {

	logger("creates ListUpdate for location", { pathname: location.pathname });

	const configuredUpdateMethod = listInstance.update({
		beforeUpdate: () => {
		},
		afterUpdate: () => {
			history.push({
				pathname: location.pathname,
				search: "?" + listInstance.getUrlSearchParams()
			});
		}
	});

	return {
		toPage(selected) {
			logger("updateList's Page", { page: selected.toString(10) });
			return configuredUpdateMethod({type: "page" }, { page: selected.toString(10) });
		},
		toSort(value) {
			logger("updateList's sort", { sort: value });
			return configuredUpdateMethod({type: "sort" }, { sort: value });
		},
		toOrderedSort(value) {
			logger("updateList's sort by select", { sort: value });
			return configuredUpdateMethod({type: "select-sort" }, { sort: value });
		},
		toTab(tabKey = "status") {
			return function(value) {
				logger("updateList's tabKey " + tabKey, {[tabKey] : value });
				return configuredUpdateMethod({type: "tab" }, {[tabKey] : value });
			};
		},
		toQuery(queryKey, validator) {
			return function (value) {
				const clearValue = typeof validator === "function" ? validator(value) : value;

				logger("updateList's Query ", { sourceValue : value, cleanValue: clearValue });

				return configuredUpdateMethod({type: "query" }, {[queryKey] : clearValue });
			};
		},
		toFilter(getFilters) {
			const nextFilters = getFilters();
			logger("to Filter payload ", nextFilters);
			return configuredUpdateMethod({type: "filter" }, nextFilters);
		}
	};
}

export { createListUpdater };
