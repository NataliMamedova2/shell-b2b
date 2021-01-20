import React from "react";
import {Tabs, TabsItem} from "../../../ui/Tabs";
import { useHistory,} from "react-router-dom";
import { useTranslation } from "react-i18next";

type Props = {
	tab: "company" | "cards"
}

const TransactionsTabs = ({ tab }: Props) => {
	const { t } = useTranslation();
	const history = useHistory();

	return (
		<Tabs
			className="m-transactions-list__tabs"
			onChange={(value) => history.push(`/transactions/${value}`) }
			activeValue={tab}
			defaultValue="company"
		>
			<TabsItem value="company">{ t("Company") } </TabsItem>
			<TabsItem value="cards">{ t("Cards") }</TabsItem>
		</Tabs>
	);
};

export default TransactionsTabs;
