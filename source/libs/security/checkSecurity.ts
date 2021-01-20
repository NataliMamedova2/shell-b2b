import {isAllowedProtocol} from "./isAllowedProtocol";
import {isAllowedEnvironment} from "./isAllowedEnvironment";
import {isAllowedOrigin} from "./isAllowedOrigin";

const checkSecurity =  async (): Promise<boolean> => {
	return Promise.all([
		isAllowedEnvironment,
		isAllowedOrigin,
		isAllowedProtocol
	])
		.then(res => Promise.resolve(true))
		.catch(e => Promise.reject(false));
};

export { checkSecurity };
