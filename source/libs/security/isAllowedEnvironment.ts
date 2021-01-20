const isAllowedEnvironment = (): Promise<boolean> => {
	if( typeof document === "undefined" || typeof window === "undefined" ) {
		return Promise.reject(false);
	}
	return Promise.resolve(true);
};

export { isAllowedEnvironment };
