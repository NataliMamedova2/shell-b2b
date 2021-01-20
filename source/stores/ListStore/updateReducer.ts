import {ORDER_ASC, ORDER_DESC, TSearchParams} from "./ListStore";
import {logger} from "../../libs";

export type TReducerPayload = {
	[key: string]: string | string[]
};
export type TReducerAction = {
	type: "sort" | "select-sort" | "page" | "filter" | "query" | "tab"
};
const getSortParams = (nextSort: string, params: TSearchParams): TSearchParams => {
	const { sort, order } = params;

	if(sort === nextSort) {
		const newOrder = order === ORDER_ASC ? ORDER_DESC : ORDER_ASC;
		return  { order: newOrder, sort: nextSort };
	}
	return { order: ORDER_DESC, sort: nextSort };
};

const getSortParamsFromSelect = (stringPayload: string) => {
	const [sort, order] = stringPayload.split("_");
	return { order, sort };
};

const updateReducer = (
	action: TReducerAction,
	payload: TReducerPayload,
	defaultParams: TSearchParams,
	currentParams: TSearchParams
) : TSearchParams => {

	const payloadKeys: string[] = Object.keys(payload);
	const firstKey = payloadKeys[0] || action.type;
	const firstValue = payload[firstKey];

	logger("UPDATE REDUCER " + action.type, {action, defaultParams, currentParams, payload});

	switch (action.type) {
		case "sort":
			return {
				...currentParams,
				...getSortParams(firstValue as string, currentParams)
			};
		case "select-sort":
			return {
				...currentParams,
				...getSortParamsFromSelect(firstValue as string)
			};
		case "page":
			return {
				...currentParams,
				page: (parseInt(firstValue as string) + 1).toString(10)
			};
		case "filter":
			return {
				page: "1",
				sort: currentParams.sort,
				order: currentParams.order,
				...payload
			};
		case "query":
			return {
				...currentParams,
				page: "1",
				[firstKey || "q"]: decodeURIComponent(firstValue as string)
			};
		case "tab":
			return {
				...currentParams,
				page: "1",
				[firstKey]: firstValue
		};
		default:
			return {
				...currentParams,
				...payload
			};
	}

};

export default updateReducer;
