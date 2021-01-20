import React, {ReactNode, Component} from "react";
import View from "../../../components/View";
import {FormLayout, FormLayoutAside, FormLayoutMain, FormLayoutInfo} from "../../../components/FormLayout";
import Paper from "../../../ui/Paper";
import "./styles.scss";
import SimpleForm from "../../../components/SimpleForm";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import Back from "../../../ui/Back/Back";
import {
	CREATE_USER_FORM_GRID,
	createUserFormConfig,
	matchPasswords,
} from "../config";
import { withTranslation, WithTranslation } from "react-i18next";
import {STAFF_ACTION_CREATE} from "../../../stores/StaffStore/config";
import usersStore from "../usersStore";
import { withRouter, RouteComponentProps } from "react-router-dom";
import {observer} from "mobx-react";
import {API_ERROR_SERVER} from "../../../config/errors";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode
} & WithTranslation & RouteComponentProps


class UserCreate extends Component<Props> {

	render() {
		const { t } = this.props;
		const { isActionSuccess, isActionError, actionPending, errorPayload } = usersStore.staff;

		return (
			<View className="m-user-create">
				<PageTitle contentString={t("Creating user profile")} />
				<div className="m-user-create__header">
					<Back to="/users" />
				</div>

				<FormLayout>
					<FormLayoutAside>
						<FormLayoutInfo
							icon="profile"
							title={t("Creating user profile")}
							text={t("Creating user profile (description)")} />
					</FormLayoutAside>

					<FormLayoutMain>
						<Paper>
							<SimpleForm
								key="user-create"
								storedData={[]}
								onValidate={matchPasswords}
								pending={actionPending}
								errors={errorPayload.validations}
								config={createUserFormConfig(CREATE_USER_FORM_GRID)}
								onSubmit={(data) => usersStore.staff.create(data)}
								submitLabel={t("Create")}
							/>
						</Paper>
					</FormLayoutMain>
				</FormLayout>

				{ isActionSuccess(STAFF_ACTION_CREATE) && (
					<PopupAlert onConfirm={this.hideMessage} title={ t("User was created")} confirmLabel={t("Ok")} />
				)}

				{ isActionError("*") && errorPayload.statusCode === API_ERROR_SERVER && (
					<PopupAlert
						onConfirm={usersStore.staff.clearActionState}
						title={ t("Oops!")}
						description={ t("Something went wrong. Please, try later.") } confirmLabel={t("Ok, I'll try later")} />
				)}

			</View>
		);
	}

	hideMessage = () => {
		usersStore.staff.clearActionState();
		this.props.history.push("/users");
	};

	submitHandler = (data: TSimpleFormData) => {
		this.setState({
			data: data,
			sent: true
		});
	};
}

export default withTranslation()(withRouter(observer(UserCreate)));
