import {TMultiSelectOption} from "../../../ui/MultiSelect/MultiSelect";

export const STATS_ITEMS_FETCH = "FETCH_STATS_ITEMS";
export const STATS_ITEMS_SUCCESS = "SUCCESS_STATS_ITEMS";
export const STATS_ITEMS_ERROR = "ERROR_STATS_ITEMS";

type TAction = {
	type: typeof STATS_ITEMS_ERROR | typeof STATS_ITEMS_FETCH | typeof STATS_ITEMS_SUCCESS,
	items?: any,
}


type TState = {
	pending: boolean,
	items: TTMCItems,
	error: boolean
}

export type TTMCItems = {
	[key: string]: TMultiSelectOption[]
}

const initialItemsStatsState = {
	pending: false,
	error: false,
	items: {},
};

const itemsStatsReducer = (state: TState, action: TAction) => {
	switch (action.type) {
		case STATS_ITEMS_FETCH: {
			return {
				...state,
				pending: true,
			};
		}
		case STATS_ITEMS_SUCCESS:
			return {
				...state,
				pending: false,
				items: action.items || [],
				error: false
			};
		case STATS_ITEMS_ERROR:
			return {
				...state,
				pending: false,
				items: [],
				error: true
			};
		default:
			throw new Error("No handler for action type" + action.type);

	}
};


export { itemsStatsReducer, initialItemsStatsState };
