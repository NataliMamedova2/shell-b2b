import React from "react";
import PopupAlert from "../../ui/Popup/PopupAlert";
import {useTranslation} from "react-i18next";

const defaultProps = {};

type Props = {
	when: boolean,
	onClose: () => void,
	detail?: {
		statusCode: number
	}
}

const ServerErrorMessage = ({ when, onClose, detail }: Props) => {
	const { t } = useTranslation();

	if(!when) {
		return null;
	}
	const message = detail ? `Type of error: "${detail.statusCode}"` : "";

	return (
		<PopupAlert
			onConfirm={onClose}
			title={ t("Oops!")}
			description={ `${t("Something went wrong. Please, try later.")} ${message}` }
			confirmLabel={t("Ok, I'll try later") } />
	);
};

ServerErrorMessage.defaultProps = defaultProps;

export default ServerErrorMessage;
