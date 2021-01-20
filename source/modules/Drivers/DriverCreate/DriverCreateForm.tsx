import React, {Component} from "react";
import "./styles.scss";
import {FormLayout, FormLayoutAside, FormLayoutInfo, FormLayoutMain} from "../../../components/FormLayout";
import SimpleForm from "../../../components/SimpleForm";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import createDriverEditForm  from "../config";
import {STAFF_ACTION_CREATE} from "../../../stores/StaffStore/config";
import driversStore from "../driversStore";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import {withTranslation, WithTranslation} from "react-i18next";
import {observer} from "mobx-react";
import {API_ERROR_SERVER} from "../../../config/errors";
import {preparingDriverDataForSubmit} from "../prepareDriverDataForSubmit";

type Props = {
	onSubmit: (data: TSimpleFormData) => void
	onCancel?: () => void,
	onConfirm?: () => void,
	showCreatedMessage: boolean
} & WithTranslation

class DriverCreateForm extends Component<Props> {
	state = {
		data: {},
		sent: false
	};

	render() {

		const { t, showCreatedMessage } = this.props;
		const { actionPending, isActionSuccess, isActionError, errorPayload } = driversStore.staff;

		return (
			<FormLayout>
				<FormLayoutAside>
					<FormLayoutInfo
						title={t("Create driver profile")}
						icon="profile"
						text={t("Create driver profile [description]")}
					/>
				</FormLayoutAside>
				<FormLayoutMain>
					<SimpleForm
						storedData={{}}
						pending={ actionPending }
						config={createDriverEditForm}
						submitLabel={ t("Create") }
						onSubmit={ this.submitHandler }
						errors={errorPayload.validations}
						cancelLabel={ t("Cancel")}
						onCancel={this.props.onCancel ? this.props.onCancel : undefined}
					/>
				</FormLayoutMain>

				{ showCreatedMessage && isActionSuccess(STAFF_ACTION_CREATE) && (
					<PopupAlert onConfirm={this.confirmAlert} title={ t("Driver was created")} confirmLabel={t("Ok")} />
				)}

				{ isActionError("*") && driversStore.staff.errorPayload.statusCode === API_ERROR_SERVER && (
					<PopupAlert
						onConfirm={this.confirmAlert}
						title={ t("Oops!")}
						description={ t("Something went wrong. Please, try later.") } confirmLabel={t("Ok, I'll try later")} />
				)}
			</FormLayout>
		);
	}
	submitHandler = (data: TSimpleFormData) => {
		driversStore.staff.create(preparingDriverDataForSubmit(data), this.props.onSubmit);
	};

	confirmAlert = () => {
		driversStore.staff.clearActionState();
		if(this.props.onConfirm) this.props.onConfirm();
	}

}

export default withTranslation()(observer(DriverCreateForm));
