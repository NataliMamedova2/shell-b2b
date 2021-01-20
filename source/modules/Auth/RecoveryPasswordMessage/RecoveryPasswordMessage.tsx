import React, {ReactNode} from "react";
import {ClearHeader} from "../../../components/Header";
import {H1, H4, Paragraph} from "../../../ui/Typography";
import Button from "../../../ui/Button";
import {useTranslation} from "react-i18next";
import ScrollToZero from "../../../ui/ScrollToZero";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode
}

const RecoveryPasswordMessage = ({children, ...props}: Props) => {
	const { t } = useTranslation();
	return (
		<div className="m-sign m-sign--yellow">
			<ScrollToZero />
			<PageTitle contentString={`${t("Recovery password")} - ${t("Almost done!")}`} />
			<div className="m-sign__header">
				<ClearHeader/>
			</div>

			<div className="m-sign__message">
				<H1 className="m-sign__title">{ t("Almost done!") }</H1>
				<H4 className="m-sign__lead">{ t("We've sent the email with the new password or a link to the recovery password page to your email address.") }</H4>
				<Paragraph className="m-sign__lead">{ t("Visit your inbox and search our list") }</Paragraph>
				<Button to="/auth">{ t("To authorization") }</Button>
			</div>
		</div>
	);
};

export default RecoveryPasswordMessage;
