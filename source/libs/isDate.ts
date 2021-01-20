const isDate = (source: string | number): boolean => {

	return new Date(source).getTime() > 0;
};

export { isDate };
