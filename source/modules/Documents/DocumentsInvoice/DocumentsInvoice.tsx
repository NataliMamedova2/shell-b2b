import React, {ReactNode} from "react";
import "./styles.scss";
import InvoiceLargeLink from "../InvoiceLargeLink";
import InvoicePageLayout from "../InvoicePageLayout";
import {useTranslation} from "react-i18next";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode
}

const DocumentsInvoice = ({children, ...props}: Props) => {
	const { t } = useTranslation();

	return (
		<InvoicePageLayout to="/documents">
			<PageTitle contentString={t("Account Formation Application")} />
			<InvoiceLargeLink to="custom" icon="check-square" title={ t("Manually pay this amount") } />
			<InvoiceLargeLink to="calculation" icon="doc" title={ t("Volume payment calculation") } />
		</InvoicePageLayout>
	);
};

export default DocumentsInvoice;
