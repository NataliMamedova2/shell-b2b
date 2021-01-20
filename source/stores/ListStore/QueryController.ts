import {toJS} from "mobx";
import {TSearchParams} from "./ListStore";

class QueryController {
	private params: URLSearchParams;

	constructor( private search: string) {
		this.params = new URLSearchParams(search);
	}

	getPagination = () => {
		if(this.params.has("page")) {
			return this.params.get("page");
		}
	};
	getSort = () => {};
	getFilters = () => {};
	getQuery = () => {};

	static getHashes = (search: string) => {
		return decodeURIComponent( search.slice(search.indexOf("?") + 1) )
			.replace("[]", "")
			.split("&");
	};

	static getParamsFromSearch = (search: string, arrayKeys: string[]): TSearchParams => {
		const hashes = QueryController.getHashes(search);

		return hashes.reduce((acc: {[key: string]: any}, current: string) => {
			const [key, value] = current.split("=");

			if(!key || !value) {
				return acc;
			}

			if(arrayKeys.includes(key)) {
				if(!Array.isArray(acc[key])) {
					acc[key] = [];
				}
				acc[key].push(value);
			} else {
				acc[key] = value;
			}

			return acc;
		}, {});
	};

	static getUrlSearchParams = (params: TSearchParams, defaultParams: TSearchParams) => {
		const searchParams = new URLSearchParams();
		const plainParams = toJS(params);

		Object.entries(plainParams).forEach(([key, value]) => {
			if(value && defaultParams[key] !== value) {

				if(typeof value === "string") {
					searchParams.set(key, value);
				}

				if(Array.isArray(value)) {
					value.forEach((item) => {
						searchParams.append(`${key}`, item);
					});
				}
			}
		});
		return searchParams.toString();
	}

}

export default QueryController;
