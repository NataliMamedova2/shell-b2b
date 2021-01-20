import axios, {AxiosError, AxiosResponse} from "axios";
import {getRequestError} from "./requestError";
import {logger} from "./logger";
import {captureException} from "../vendors/exeptions";
import appAuthStore from "../stores/AppAuthStore";

type TPost = {
	endpoint: string,
	params?: any,
	responseType?: "blob" | "json"
}

const getFromCache = <T>(endpoint: string, cachePrefix: string): Promise<T[] | null> => {
	const data: string | null = localStorage.getItem("".concat(cachePrefix, endpoint));

	if(data) {
		return Promise.resolve(JSON.parse(data));
	}
	return Promise.reject(null);
};

const setToCache = <T>(endpoint: string, cachePrefix: string, data: any): void => {
	localStorage.setItem("".concat(cachePrefix, endpoint), JSON.stringify(data));
};

const get = <T = any>({ endpoint, params = {}, responseType = "json" }: TPost, isRelative: boolean = true): Promise<AxiosResponse<T>> => {
	return new Promise(async (resolve, reject) => {
		const url = isRelative ? "/api/v1" + endpoint : endpoint;

		axios.get( url, {
			params: {...params},
			headers: {
				"Authorization": "Bearer " +  appAuthStore.token
			},
			responseType: responseType
		})
			.then(resolve)
			.catch((e: AxiosError) => {
				const response: any = e.response ? e.response : { status: 0 };
				if(response.status !== 401) {
					logger("/GET error", { e, res: response });
					captureException(e);
				}
				if(e.response) {
					getRequestError(e.response.status);
					reject(e.response);
				} else {
					reject(null);
				}
			});
	});
};

export { get, getFromCache, setToCache };
