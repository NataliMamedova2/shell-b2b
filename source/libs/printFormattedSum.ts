import { formatNumber } from "./formatNumber";
import { withCurrency } from "./withCurrency";

const toPrice = (amount: number) => amount / 100;

const normalizeAmount = (amount: number) => {
	return formatNumber(toPrice(amount));
};

const printFormattedSum = (amount: number, currency: boolean = true): string => {
	const normalizedAmount = toPrice(amount);

	if(currency) {
		return withCurrency(formatNumber(normalizedAmount));
	}
	return formatNumber(normalizedAmount);
};

export { printFormattedSum, toPrice, normalizeAmount };
