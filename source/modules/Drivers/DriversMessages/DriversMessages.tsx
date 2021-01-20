import React, {Fragment} from "react";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_DELETE, STAFF_ACTION_UPDATE} from "../../../stores/StaffStore/config";
import PopupConfirm from "../../../ui/Popup/PopupConfirm";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import driversStore from "../driversStore";
import {withTranslation, WithTranslation} from "react-i18next";
import {observer} from "mobx-react";
import {API_ERROR_SERVER} from "../../../config/errors";

type Props = {
	afterDelete: () => void
	afterChangeStatus: () => void
} & WithTranslation

const DriversMessages = ({afterDelete, afterChangeStatus ,t}: Props) => {

	const {
		actionPayload,
		isAction,
		isActionSuccess,
		isActionError,
		actionPending,
		clearAction,
		errorPayload
	} = driversStore.staff;

	const getOppositeStatus = () => {
		return (actionPayload && actionPayload.detail)
			? actionPayload.detail.status === "active"
				? "blocked"
				: "active"
			: "active";
	};

	const storeStaff = driversStore.staff;

	return (
		<Fragment>

			{ isAction(STAFF_ACTION_CHANGE_STATUS) && (
				<PopupConfirm
					title={t("Change driver status?")}
					onCancel={clearAction}
					cancelLabel={ t("No")}
					confirmLabel={t("Yes")}
					pending={actionPending}
					onConfirm={() => storeStaff.changeStatus(getOppositeStatus())} />
			)
			}

			{ isAction(STAFF_ACTION_DELETE) && (
				<PopupConfirm
					title={t("The driver will be deleted. Are you sure?")}
					onCancel={storeStaff.clearAction}
					cancelLabel={ t("No")}
					confirmLabel={t("Yes")}
					pending={actionPending}
					onConfirm={storeStaff.delete} />
			)
			}

			{ isActionSuccess(STAFF_ACTION_CHANGE_STATUS) && (
				<PopupAlert
					title={t("Driver status changed")}
					description={t("The changes will take effect after the administrator has processed them")}
					confirmLabel={t("Ok")}
					onConfirm={afterChangeStatus} />
			)
			}

			{ isActionSuccess(STAFF_ACTION_UPDATE) && (
				<PopupAlert
					title={t("Driver profile was updated")}
					confirmLabel={t("Ok")}
					onConfirm={storeStaff.clearActionState} />
			)
			}

			{ isActionSuccess(STAFF_ACTION_DELETE) && (
				<PopupAlert
					title={t("Driver was Deleted")}
					confirmLabel={t("Ok")}
					onConfirm={afterDelete} />
			)}

			{ (isActionError("*") && errorPayload.statusCode === API_ERROR_SERVER ) && (
				<PopupAlert
					onConfirm={storeStaff.clearActionState}
					title={ t("Oops!")}
					description={ `${t("Something went wrong. Please, try later.")} Type of error: "${errorPayload.statusCode}"` }
					confirmLabel={t("Ok, I'll try later")} />
			)}
		</Fragment>
	);
};

export default withTranslation()(observer(DriversMessages));
