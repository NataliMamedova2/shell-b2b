const isAllowedOrigin = (): Promise<boolean> => {
	if(window.location.origin === "file://") {
		return Promise.reject(false);
	}
	return Promise.resolve(true);
};

export { isAllowedOrigin };
