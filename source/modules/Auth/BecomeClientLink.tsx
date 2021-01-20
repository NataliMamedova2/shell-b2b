import React from "react";
import {DEFAULT_LANGUAGE} from "../../environment";
import {getCurrentLanguage} from "../../config/i18n/getCurrentLanguage";
import {useTranslation} from "react-i18next";

const BecomeClientLink = React.memo(() => {
	const lang = getCurrentLanguage();
	const { t } = useTranslation();
	const url = lang === DEFAULT_LANGUAGE ? process.env.MARKETING_BECOME_CLIENT : process.env.MARKETING_BECOME_CLIENT_EN;

	return (
		<a className="c-text c-text--paragraph a-color-dark"
		   href={url}
		   rel="noopener noreferrer"
		   target="_blank"
		   type="paragraph">{ t("Sign Up") }</a>
	);
});
export default BecomeClientLink;
