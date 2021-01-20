export const API_ERROR_SERVER = "api-server-error";
export const API_ERROR_AUTH = "api-auth-error";
export const API_ERROR_VALIDATIONS = "api-validation-error";

export type TResponseErrorType = typeof API_ERROR_SERVER | typeof  API_ERROR_AUTH | typeof API_ERROR_VALIDATIONS;

type TAPIError = {
	[code: number]: TResponseErrorType
}

const ERRORS_CONFIG: TAPIError = {
	// server
	500: API_ERROR_SERVER,
	404: API_ERROR_SERVER,
	// auth
	401: API_ERROR_AUTH,
	// validations
	400: API_ERROR_VALIDATIONS
};

export const getErrorType = (code: number): TResponseErrorType => {
	if ( code in ERRORS_CONFIG ) {
		return ERRORS_CONFIG[code];
	}
	throw new Error();
};
