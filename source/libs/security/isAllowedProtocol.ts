const whitelistedProtocols = ["http", "https"];
const isAllowedProtocol = (): Promise<boolean> => {

	const { protocol } = window.location;
	const inWhitelistedProtocols = whitelistedProtocols.some(whitelistedProtocol => {
		return whitelistedProtocol === protocol || whitelistedProtocol.concat(":") === protocol;
	});

	if(inWhitelistedProtocols) {
		return Promise.resolve(inWhitelistedProtocols);
	}

	return Promise.reject(inWhitelistedProtocols);
};


export { isAllowedProtocol };
