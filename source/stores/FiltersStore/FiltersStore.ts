import {action, computed, decorate, observable, toJS} from "mobx";
import omit from "lodash/omit";
import {TSearchParams} from "../ListStore/ListStore";
import QueryController from "../ListStore/QueryController";

export type TFiltersData = {
	[propetry: string]: string | string[]
};

export interface IFiltersStore {
	readonly formShown: boolean;
	applied: TFiltersData;
	showForm: () => void;
	hideForm: () => void;
	submit: (filteredData: TFiltersData) => void;
	init: (data: TSearchParams, filterKeys: string[]) => void;
	reset: () => void;
	remove: (key: string) => void;
	readonly appliedExists: boolean,
	getApplied: () => TFiltersData
}

class FiltersStore implements IFiltersStore {
	formShown: IFiltersStore["formShown"] = false;
	applied: IFiltersStore["applied"] = {};

	showForm: IFiltersStore["showForm"] = () => (this.formShown = true);
	hideForm: IFiltersStore["hideForm"] = () => (this.formShown = false);

	submit: IFiltersStore["submit"] = (filteredData) => {
		this.applied = filteredData;
		this.hideForm();
	};

	init: IFiltersStore["init"] = (params,filterKeys) => {
		const paramsEntries = Object.entries(params);

		this.applied = paramsEntries.reduce((acc: TFiltersData, [key, value]): TFiltersData => {
			if(filterKeys.includes(key)) {
				acc[key] = value;
			}
			return acc;
		}, {});
	};

	reset: IFiltersStore["reset"] = () => {
		this.applied = {};
		this.hideForm();
	};
	remove: IFiltersStore["remove"] = (key: string) => {
		const nextApplied = omit(toJS(this.applied), [key]);

		this.applied = { ...nextApplied };
	};

	getApplied = (): TFiltersData => {
		return {...toJS(this.applied)};
	};

	getAppliedParams = () => {
		return QueryController.getUrlSearchParams(this.applied, {});
	};

	get appliedExists () {
		return Object.keys(this.applied).length > 0;
	}
}

decorate(FiltersStore, {
	formShown: observable,
	applied: observable,
	showForm: action,
	hideForm: action,
	submit: action,
	init: action,
	reset: action,
	remove: action,
	appliedExists: computed
});

export default FiltersStore;
