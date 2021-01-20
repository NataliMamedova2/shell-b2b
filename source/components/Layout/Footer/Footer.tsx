import React from "react";
import { Paragraph } from "../../../ui/Typography";
import Logo from "../../Logo";
import "./styles.scss";
import {useTranslation} from "react-i18next";

type Props = {
	children?: React.Component
}

const Footer = ({children, ...props}: Props) => {
	const { t } = useTranslation();

	return (
		<footer className="m-footer">
			<div className="m-footer__section m-footer__section--copy">
				<Logo />
				<Paragraph>{ t("Shell Retail Ukraine all rights reserved") } ®</Paragraph>
			</div>
			<div className="m-footer__section m-footer__section--rules">
				<Paragraph as="a" href="https://www.shell.ua/політика-щоaо-файлів-cookie.html" target="_blank">{ t("Public Offer Agreement") }</Paragraph>
				<Paragraph as="a" href="https://www.shell.ua/положення-і-умови.html" target="_blank">{ t("Terms and Conditions") }</Paragraph>
				<Paragraph as="a" href="https://www.shell.ua/політика-дотримання-конфіденційності.html" target="_blank">{ t("Privacy Notice") }</Paragraph>
			</div>
		</footer>
	);
};

export default Footer;
