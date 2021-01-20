import {TSearchParams} from "../stores/ListStore/ListStore";

function getUrlParams(search: string, arrayKeys: string[] = []): TSearchParams {
	let hashes = decodeURIComponent( search.slice(search.indexOf("?") + 1) )
		.replace("[]", "")
		.split("&");
	
	return hashes.reduce((acc: {[key: string]: any}, current: string) => {
		const [key, value] = current.split("=");

		if(!key || !value) {
			return acc;
		}

		if(arrayKeys.includes(key)) {
			if(!acc[key]) {
				acc[key] = [value];
			} else  {
				acc[key] = [...acc[key], value];
			}
		}
		acc[key] = value;

		return acc;
	}, {});
}

export { getUrlParams };
