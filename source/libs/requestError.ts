import appAuthStore from "../stores/AppAuthStore";

const getRequestError = (status: number) => {
	// auth
	if(status === 401) {
		appAuthStore.logOut();
	}
	// not found
	if(status === 404) {
		window.location.href = window.location.origin + "/error/not-found";
	}
};

const postRequestError = (status: number) => {
	if(status === 401) {
		appAuthStore.logOut();
	}
};

export { getRequestError, postRequestError };
