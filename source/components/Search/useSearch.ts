import {useEffect, useReducer} from "react";
import {get} from "../../libs";

const SEARCH_ON_START = "search_set_pending";
const SEARCH_ON_RESULT = "search_on_result";
const SEARCH_ON_ERROR = "search_on_error";

type TSearchState = {
	pending: boolean,
	empty: boolean,
	error: boolean,
	data: any[]
}

type TSearchAction = {
	type: string,
	payload?: any[]
}

export type TSearchCache = {
	has(key: string): boolean,
	set(key: string, data: any[]): void,
	get(key: string): any[]
}

const searchInitialState: TSearchState = {
	pending: true,
	empty: false,
	error: false,
	data: []
};

function searchReducer (state: TSearchState = searchInitialState, action: TSearchAction ) {
	const data = action.payload || [];

	switch (action.type) {
		case SEARCH_ON_START: {
			return {
				...state,
				pending: true
			};
		}
		case SEARCH_ON_RESULT: {
			return {
				...state,
				pending: false,
				data: data,
				empty: data.length === 0
			};
		}
		case SEARCH_ON_ERROR: {
			return {
				...state,
				error: true,
				pending: false,
				data: [],
				empty: true,
			};
		}
		default:
			throw new Error("Wrong action type");
	}
}

function useSearch <T>(endpoint: string, query: string, cache?: TSearchCache) {
	const [ state, dispatch] = useReducer(searchReducer, searchInitialState);

	useEffect(() => {
		let isCanceled: boolean = false;
		let cacheKey: string = endpoint + query;
		dispatch({ type: SEARCH_ON_START });

		if(cache && cache.has(cacheKey)) {
			dispatch({ type: SEARCH_ON_RESULT, payload: cache.get(cacheKey)});
		} else {
			get({endpoint, params: { q: query }})
				.then((res) => {
					if(!isCanceled) {
						dispatch({ type: SEARCH_ON_RESULT, payload: res.data });

						if(cache) {
							cache.set(cacheKey, res.data);
						}
					}
				})
				.catch(() => {
					dispatch({ type: SEARCH_ON_ERROR});
				});
		}
		return () => {
			isCanceled = true;
		};
	}, [cache, endpoint, query]);

	return state;
}

const createSearchCache = (): TSearchCache => {
	const store: { [key: string]: any } = {};

	const has = (key: string) => typeof store[key] !== "undefined";
	const get = (key: string) => {
		return has(key) ? store[key] : [];
	};

	const set = (key: string, data: any[]) => {
		store[key] = data;
	};

	return { has, get, set };
};

export { useSearch, createSearchCache };
