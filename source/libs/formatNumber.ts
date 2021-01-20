function formatNumber(num: number, thousandDivider: string = " "): string {
	let parts = num.toString().split(".");

	/**
	 * before .
	 * divide every 3-digit group with `thousandDivider`
	 */
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandDivider);

	/**
	 * after .
	 * setup trailing 00 after dot
	 */
	parts[1] = parts[1]
		? parts[1].length === 1
			? `${parts[1][0]}0`
			: parts[1]
		: "00";

	return parts.join(".");
}

export { formatNumber };
