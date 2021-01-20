import {multiSelectToOptions, TMultiSelectOption} from "../../../ui/MultiSelect/MultiSelect";

export const ACTION_SELECT_TYPE = "STATS_SELECT_TYPE";
export const ACTION_SELECT_ITEM = "STATS_SELECT_TMC_ITEM";

export type TStatsReducerState = {
	type: TMultiSelectOption[],
	items: TMultiSelectOption[]
}

export type TStatsActions = {
	type: typeof ACTION_SELECT_TYPE | typeof ACTION_SELECT_ITEM,
	value: string[],
	allItems?: any
}

const statsTypes: TMultiSelectOption[] = [
	{ value: "fuels", longName: "Fuels items", shortName: "Fuels" },
	{ value: "goods", longName: "Goods items", shortName: "Goods" },
	{ value: "services", longName: "Services items", shortName: "Services" }
];

const initialStatsState: TStatsReducerState = {
	type: statsTypes,
	items: []
};

const filterItemsByType = (value: string[]) => (item: TMultiSelectOption) => {
	return item.type ? value.includes(item.type) : false;
};

const statsReducer = (state: TStatsReducerState, { type, value, allItems }: TStatsActions ) => {

	switch (type) {
		case ACTION_SELECT_TYPE: return (() => {
			return {
				...state,
				items: state.items.filter(filterItemsByType(value)),
				type: value ? multiSelectToOptions(value, statsTypes) : []
			};
		})();
		case ACTION_SELECT_ITEM: return (() => {
			return {
				...state,
				items: value ? multiSelectToOptions(value, allItems) : []
			};
		})();
		default:
			throw new Error("No found action with type" + type);
	}
};

export { statsReducer, statsTypes, initialStatsState };
