import React, {useEffect} from "react";
import "./styles.scss";
import View from "../../../components/View";
import DocumentsTabs from "../DocumentsTabs";
import PageTitle from "../../../components/PageTitle";
import {useTranslation} from "react-i18next";
import PageHeader from "../../../components/PageHeader";
import documentsStore from "../documentsStore";
import { observer } from "mobx-react";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import { Paragraph } from "../../../ui/Typography";

const getSotaSourceUrl = (token: string): string => `${process.env.PUBLIC_SOTA_SERVER_URL}/account/portallogin?code=${process.env.PUBLIC_SOTA_MERCHANT_ID}&token=${token}`;

const DocumentsForSign = () => {
	const { t } = useTranslation();

	useEffect(() => {
		documentsStore.getSotaToken();
	}, []);

	return (
		<View className="m-documents-for-sign">
			<PageTitle contentString={ t("Documents for sign") } />
			<PageHeader title={t("Documents for sign")} />
			<div className="m-documents-list__body">
				<DocumentsTabs tab="for-sign" />

				{
					documentsStore.sotaTokenPending && (
						<div className="m-documents-for-sign__loader-wrapper">
							<PendingIcon />
							<Paragraph>{ t("Connecting to server") }</Paragraph>
						</div>
					)
				}

				{
					!documentsStore.sotaTokenPending
					&& documentsStore.sotaToken === null && (
						<div className="m-documents-for-sign__loader-wrapper">
							<Paragraph color="error">{ t("Can't connect to server. Please, try again later") }</Paragraph>
						</div>
					)
				}

				{
					!documentsStore.sotaTokenPending
					&& documentsStore.sotaToken !== null
					&& (
						<div className="m-documents-for-sign__iframe-wrapper">
							<iframe title=" " className="m-documents-for-sign__iframe" src={getSotaSourceUrl(documentsStore.sotaToken)} width="100%" height="100vh" />
						</div>
					)
				}
			</div>
		</View>
	);
};

export default observer(DocumentsForSign);
