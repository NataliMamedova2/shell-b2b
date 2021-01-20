import property from "lodash/property";

const propOf = <T = string, U = T>( obj: any, propLens: string, fallback: T, transform?: (value: T) => U): string | any => {
	const getProp = property(propLens);
	const value = getProp(obj);

	return value
		? transform
			? transform(value as T)
			: value
		: fallback;
};


export { propOf };
