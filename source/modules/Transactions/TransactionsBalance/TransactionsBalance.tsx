import React, {Fragment} from "react";
import Callout from "../../../ui/Callout";
import {H4, Paragraph} from "../../../ui/Typography";
import {useTranslation} from "react-i18next";
import {printFormattedSum} from "../../../libs";

type Props = {
	accountBalance: {
		value: number,
		sign: "+" | "-" | string
	} | string,
}

const TransactionsBalance = ({accountBalance}: Props) => {
	const { t } = useTranslation();

	const printVal =
		accountBalance && typeof accountBalance !== "string"
			? `${accountBalance.sign}${printFormattedSum(accountBalance.value)}`
			: accountBalance;

	return (
		<Fragment>
			<Callout type="simple">
				{ t("Transaction adjustments may be delayed") }
			</Callout>

			<div className="m-transactions-list__score">
				<Paragraph>{t("Account balance")}</Paragraph>
				<H4>{printVal}</H4>
			</div>
		</Fragment>
	);
};

export default TransactionsBalance;
