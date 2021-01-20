import React, {Fragment} from "react";
import {observer} from "mobx-react";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import usersStore from "../usersStore";
import PopupConfirm from "../../../ui/Popup/PopupConfirm";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_DELETE, STAFF_ACTION_UPDATE} from "../../../stores/StaffStore/config";
import {withTranslation, WithTranslation} from "react-i18next";
import {API_ERROR_SERVER} from "../../../config/errors";

type Props = {
	afterDelete: () => void
	afterChangeStatus: () => void
} & WithTranslation

const UsersMessages = ({afterDelete, afterChangeStatus ,t}: Props) => {

	const {
		actionPayload,
		isAction,
		isActionSuccess,
		isActionError,
		actionPending,
		clearAction,
		errorPayload
	} = usersStore.staff;

	const getOppositeStatus = () => {
		return (actionPayload && actionPayload.detail)
			? actionPayload.detail.status === "active"
				? "blocked"
				: "active"
			: "active";
	};

	return (
		<Fragment>

			{ isAction(STAFF_ACTION_CHANGE_STATUS) && (
					<PopupConfirm
						title={t("Change user status?")}
						onCancel={clearAction}
						cancelLabel={ t("No")}
						confirmLabel={t("Yes")}
						pending={actionPending}
						onConfirm={() => usersStore.staff.changeStatus(getOppositeStatus())} />
				)
			}

			{ isAction(STAFF_ACTION_DELETE) && (
					<PopupConfirm
						title={t("The user will be deleted. Are you sure?")}
						onCancel={usersStore.staff.clearAction}
						cancelLabel={ t("No")}
						confirmLabel={t("Yes")}
						pending={actionPending}
						onConfirm={usersStore.staff.delete} />
				)
			}

			{ isActionSuccess(STAFF_ACTION_CHANGE_STATUS) && (
					<PopupAlert
						title={t("User status changed")}
						description={t("The changes will take effect after the administrator has processed them")}
						confirmLabel={t("Ok")}
						onConfirm={afterChangeStatus} />
				)
			}

			{ isActionSuccess(STAFF_ACTION_UPDATE) && (
					<PopupAlert
						title={t("User profile was updated")}
						confirmLabel={t("Ok")}
						onConfirm={usersStore.staff.clearActionState} />
				)
			}

			{ isActionSuccess(STAFF_ACTION_DELETE) && (
					<PopupAlert
						title={t("User was Deleted")}
						confirmLabel={t("Ok")}
						onConfirm={afterDelete} />
			)}

			{ (isActionError("*") && errorPayload.statusCode === API_ERROR_SERVER ) && (
					<PopupAlert
						onConfirm={usersStore.staff.clearActionState}
						title={ t("Oops!")}
						description={ `${t("Something went wrong. Please, try later.")} Type of error: "${errorPayload.statusCode}"` }
						confirmLabel={t("Ok, I'll try later")} />
			)}
		</Fragment>
	);
};

export default withTranslation()(observer(UsersMessages));
