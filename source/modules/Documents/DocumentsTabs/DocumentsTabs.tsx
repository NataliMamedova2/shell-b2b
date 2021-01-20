import React from "react";
import {Tabs, TabsItem} from "../../../ui/Tabs";
import { useHistory,} from "react-router-dom";
import { useTranslation } from "react-i18next";
import "./styles.scss";

type Props = {
	tab: "list" | "for-sign"
}

const DocumentsTabs = ({ tab }: Props) => {
	const { t } = useTranslation();
	const history = useHistory();

	return (
		<Tabs
			onChange={(value) => history.push(`/documents/${value}`) }
			activeValue={tab}
			defaultValue="list"
			className="c-documents-tabs"
		>
			<TabsItem value="list">{ t("Documents") } </TabsItem>
			<TabsItem value="for-sign">{ t("Documents for sign") }</TabsItem>
		</Tabs>
	);
};

export default DocumentsTabs;
