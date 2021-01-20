const logger = (msg: string, payload?: { [prop: string]: any } | any[]) => {
	if(process.env.NODE_ENV === "development") {
		if(payload) {
			console.groupCollapsed(`%c ${msg.toUpperCase()}`, "color: skyblue");
				console.groupCollapsed("JSON VIEW");
					console.log(JSON.stringify(payload, null, 4));
				console.groupEnd();
				console.group("PLAIN LOG VIEW");
					console.log(payload);
				console.groupEnd();
			console.groupEnd();
		} else  {
			console.log(msg);
		}
	}
};

export { logger };
