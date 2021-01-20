import i18n from "../config/i18n";
import config from "../config";

const withCurrency = (amount: number | string, spacer?: string): string => {
	return `${amount}${spacer || " "}${i18n.t(config.currency)}`;
};

export { withCurrency };
