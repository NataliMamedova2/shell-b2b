
const propValidate = <T = any, U = T>(
	origin: T,
	originValidate: (val: T) => boolean,
	fallback: U,
	transform?: (val: T) => U | T): any => {

	if(origin && originValidate(origin)) {
		return transform ? transform(origin) : origin;
	}
	return fallback;
};

export { propValidate };
