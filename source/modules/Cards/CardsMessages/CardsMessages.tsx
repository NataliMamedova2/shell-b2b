import React, {Fragment} from "react";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_UPDATE} from "../../../stores/StaffStore/config";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import PopupConfirm from "../../../ui/Popup/PopupConfirm";
import cardsStore from "../cardsStore";
import {withTranslation, WithTranslation} from "react-i18next";
import {observer} from "mobx-react";
import {API_ERROR_SERVER} from "../../../config/errors";

type Props = {
	confirmChangeStatus?: () => void
} & WithTranslation;

const CardsMessages = ({ confirmChangeStatus, t }: Props) => {
	const { isActionSuccess, isActionError, isAction, actionPending } = cardsStore.staff;

	const hideMessage = () => {
		cardsStore.staff.clearAction();
		cardsStore.staff.clearActionState();
	};

	const defaultConfirm = () => {
		throw Error("No method for this confirm");
	};

	return (
		<Fragment>
			{
				isAction(STAFF_ACTION_CHANGE_STATUS) && (
					<PopupConfirm
						title={t("The card will be blocked. Are you sure?")}
						onCancel={hideMessage}
						cancelLabel={ t("No")}
						confirmLabel={t("Yes")}
						pending={actionPending}
						onConfirm={confirmChangeStatus || defaultConfirm} />
				)
			}

			{
				isActionSuccess(STAFF_ACTION_CHANGE_STATUS) && (
					<PopupAlert
						title={t("Status of card changed")}
						description={t("The changes will take effect after the administrator has processed them")}
						confirmLabel={t("Ok")}
						onConfirm={hideMessage} />
				)
			}

			{ isActionSuccess(STAFF_ACTION_UPDATE)
					&& <PopupAlert title={t("Card updated")} confirmLabel={t("Ok")} onConfirm={hideMessage} />
			}


			{ isActionError("*") && cardsStore.staff.errorPayload.statusCode === API_ERROR_SERVER && (
				<PopupAlert
					onConfirm={hideMessage}
					title={ t("Oops!")}
					description={ t("Something went wrong. Please, try later.") } confirmLabel={t("Ok, I'll try later")} />
			)}
		</Fragment>
	);
};

export default withTranslation()(observer(CardsMessages));
