type TPluralConfig = {
	one: string,
	two: string,
	five: string
}

const tPlural = (count: number, config: TPluralConfig ): string => {
	const { one, two, five } = config;
	let n = Math.abs(count);

	n %= 100;
	if (n >= 5 && n <= 20) {
		return five;
	}
	n %= 10;
	if (n === 1) {
		return one;
	}
	if (n >= 2 && n <= 4) {
		return two;
	}
	return five;
};

export default tPlural;
