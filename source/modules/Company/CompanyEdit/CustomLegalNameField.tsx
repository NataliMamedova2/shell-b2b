import React from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import Tooltip from "../../../ui/Tooltip";
import { Paragraph } from "../../../ui/Typography";
import {useTranslation} from "react-i18next";


const CustomLegalNameField = ({value}: TSingleInput) => {
	const { t } = useTranslation();
	return (
		<div className="c-company-legal-name">
			<Paragraph className="c-company-legal-name__value">{ value }</Paragraph>
			<Tooltip message={t("Legal name of company can't be changed here")} size="medium" tooltipKey="company-legal-name" />
		</div>
	);
};

export default CustomLegalNameField;
