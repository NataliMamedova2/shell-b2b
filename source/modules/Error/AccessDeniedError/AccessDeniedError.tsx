import React from "react";
import "./styles.scss";
import {H1, Paragraph} from "../../../ui/Typography";
import Button from "../../../ui/Button";
import {useTranslation} from "react-i18next";


const AccessDeniedError = () => {
	const { t } = useTranslation();
	return (
		<div className="m-access-error">
			<H1 className="m-access-error__title">{ t("You do not have sufficient permissions to access this section.") }</H1>
			<Paragraph className="m-access-error__lead">{ t("Contact your administrator") }</Paragraph>

			<Button to="/">{ t("Home") }</Button>
		</div>
	);
};

export default AccessDeniedError;
