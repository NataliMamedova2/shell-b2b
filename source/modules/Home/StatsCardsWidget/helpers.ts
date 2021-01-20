import {TMultiSelectOption} from "../../../ui/MultiSelect/MultiSelect";
import {TTMCItems} from "./itemsStatsReducer";

const prepareTmcItems = (arr: Array<{ name: string, id: string }>, type: string): TMultiSelectOption[] => {

	return arr.map(item => {
		return {
			value: item.id,
			longName: item.name,
			shortName: item.name,
			type: type
		};
	});
};

const getTmcItems = (items: TTMCItems, types: TMultiSelectOption[]) => {
	return types.map(type => items[type.value]).filter(Boolean).flat();
};


export { prepareTmcItems, getTmcItems };
