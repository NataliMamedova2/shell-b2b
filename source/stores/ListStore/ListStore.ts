import {action, observable, decorate, computed, toJS} from "mobx";
import updateReducer, {TReducerAction, TReducerPayload} from "./updateReducer";
import ItemController, {IItemController} from "./ItemController";
import {get, logger} from "../../libs";
import QueryController from "./QueryController";

export const ORDER_ASC = "asc";
export const ORDER_DESC = "desc";

type TListMetaInfo = {
	pagination: {
		currentPage: number,
		totalCount: number
	},
	[key: string]: any
}

export type TListData<T = any> = {
	meta: TListMetaInfo,
	data: T[]
} | null

export type TSearchParams =  {
	[key: string]: string | string[]
};

export type TNextParams = {
	nextParams: TSearchParams
}

type TUpdate = {
	beforeUpdate?: () => void,
	afterUpdate?: () => void,
}
export type TListUpdateMethod = (action: TReducerAction, payload: TReducerPayload) => void;

export interface IListStore<T> {
	params: TSearchParams,
	pending: boolean,
	data: TListData<T>,
	items: IItemController<T>[],
	update: (d: TUpdate) => TListUpdateMethod,
	updateData: any,
	updateParams: ({ nextParams }: TNextParams) => Promise<TSearchParams>,
	getUrlSearchParams: () => string,
	getParam: (paramKey: string) => any | undefined,
	getParamIsEqual: (paramKey: string, expectValue: string) => boolean,
	metaInfo: TListMetaInfo | null
}

class ListStore<T> implements IListStore<T> {
	public params: TSearchParams;
	public pending: boolean = false;
	public data: TListData<T> = null;
	public items: IItemController<T>[] = [];

	constructor(
		private endpoint: string,
		public defaultParams: TSearchParams,
		private serializeData?: (d: any) => TListData<T>
	) {
		this.params = {...defaultParams};
	}

	public update = ({ beforeUpdate, afterUpdate }: TUpdate) => ( action: TReducerAction, payload: TReducerPayload ) => {
		if(beforeUpdate && typeof beforeUpdate === "function") {
			beforeUpdate();
		}
		const reducedParams = updateReducer(action, payload, this.defaultParams, toJS(this.params));

		this.updateParams({ nextParams: { ...reducedParams } })
			.then(() => {
				if(afterUpdate && typeof afterUpdate === "function") {
					afterUpdate();
				}
			});
	};

	public updateData = async (nextParams: TSearchParams) => {
		await this.updateParams({ nextParams });
		await this.fetchData({ nextParams });
	};

	public updateParams = ({ nextParams }: TNextParams): Promise<TSearchParams> => {

		this.params = {
			...this.defaultParams,
			...nextParams,
		};
		return Promise.resolve(this.params);
	};

	public getItemById = (id: string) => {
		return this.items.filter((item: any) => item.id === id)[0];
	};

	public filter = (func: (item: any) => boolean) => {
		const filtered = this.items.filter(func);
		this.items = filtered;

		return Promise.resolve(filtered);
	};

	getParam = (key: string): any| undefined => {
		return this.params[key];
	};

	getParamIsEqual = (paramKey: string, expectValue: string) => {
		return this.params[paramKey] === expectValue;
	};

	setPending = (bool: boolean) => (this.pending = bool);

	setData = (passedData: any) => {
		const preparedData = typeof this.serializeData === "function"
			? this.serializeData(passedData)
			: passedData;

		this.data = preparedData;
		try {
			this.items = preparedData.data.map((item: T & { id: string }) => new ItemController<T>(item.id, item));
		} catch (e) {
			logger("Error with set this.items", passedData);
		}
	};

	fetchData = async ({ nextParams }: TNextParams) => {
		this.setPending(true);

		try {
			const res = await get({ endpoint: this.endpoint, params: nextParams });
			this.setData(res.data);
		} catch (e) {
			logger("Error with data in updateData for params: ", nextParams);
		} finally {
			this.setPending(false);
		}
	};

	get metaInfo () {
		return this.data ? this.data.meta : null;
	}

	get pagination () {
		return this.data ? this.data.meta.pagination : null;
	}

	get urlSearchParams (): string {
		return QueryController.getUrlSearchParams(toJS(this.params), this.defaultParams);
	}

	getUrlSearchParams = () => {
		return QueryController.getUrlSearchParams(toJS(this.params), this.defaultParams);
	}
}

decorate(ListStore, {
	pending: observable,
	items: observable,
	data: observable,
	params: observable,
	update: action,
	updateParams: action,
	updateData: action,
	setData: action,
	fetchData: action,
	filter: action,
	setPending: action,
	pagination: computed,
	metaInfo: computed,
	urlSearchParams: computed,
});

export default ListStore;
