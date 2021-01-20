import axios, {AxiosError} from "axios";
import appAuthStore from "../stores/AppAuthStore";
import {postRequestError} from "./requestError";
import {captureException} from "../vendors/exeptions";

type TPost = {
	endpoint: string,
	data?: any
}

const post = ({ endpoint, data = {} }: TPost) => {
	return new Promise<any>(async (resolve, reject) => {

		axios.post( "/api/v1" + endpoint, data, {
			headers: {
				"Authorization": "Bearer " +  appAuthStore.token
			}
		})
			.then(resolve)
			.catch((e: AxiosError) => {
				captureException(e);
				if(e.response) {
					postRequestError(e.response.status);
					reject(e.response);
				} else  {
					reject(null);
				}
			});
	});
};

export { post };
