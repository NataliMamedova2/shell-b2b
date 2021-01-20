import PopupConfirm from "../../ui/Popup/PopupConfirm";
import React from "react";
import {useTranslation} from "react-i18next";
import useFileLoader from "../../hooks/useFileLoader";
import {TFile} from "./types";
import { useHistory } from "react-router-dom";

type Props = {
	file: TFile,
	afterLoad: () => void,
	title: string
}

const DownloadCreatedFile = ({ file, title, afterLoad }: Props) => {
	const { t } = useTranslation();
	const history  = useHistory();
	const { pending, loadFile } = useFileLoader(file.link, file.name);

	const saveHandler = async () => {
		await loadFile();
		afterLoad();
	};
	const cancelHandler = () => history.push("/documents");

	return (
		<PopupConfirm
			title={ title }
			description={ t("You can download the document or go to documents list")}
			confirmLabel={ t("Download document") }
			cancelLabel={t("Back to documents")}
			onConfirm={saveHandler}
			onCancel={cancelHandler}
			pending={pending}
		/>
	);
};

export default DownloadCreatedFile;
