import React, {ReactNode, Component, Fragment} from "react";
import View from "../../../components/View";
import {FormLayout, FormLayoutAside, FormLayoutInfoWrapper, FormLayoutMain} from "../../../components/FormLayout";
import Paper from "../../../ui/Paper";
import "./styles.scss";
import SimpleForm from "../../../components/SimpleForm";
import {  H4, Paragraph } from "../../../ui/Typography";
import { TSimpleFormData } from "@app-types/TSimpleForm";
import {
	CREATE_USER_FORM_GRID,
	createUserFormConfig,
	matchPasswords,
} from "../config";
import Badge from "../../../ui/Badge";
import {formatDate} from "../../../libs";
import Can from "../../../components/Can";
import usersStore from "../usersStore";
import {observer} from "mobx-react";
import { withTranslation, WithTranslation } from "react-i18next";
import PageHeader from "../../../components/PageHeader";
import Button from "../../../ui/Button";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_DELETE, STAFF_ACTION_UPDATE} from "../../../stores/StaffStore/config";
import UsersMessages from "../UsersMessages";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import { withRouter, RouteComponentProps } from "react-router-dom";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode,
	id: string
	data?: any
} & WithTranslation & RouteComponentProps

class UserEdit extends Component<Props> {
	state: { userData: TSimpleFormData } = {
		userData: {}
	};

	async componentDidMount() {
		const data = await usersStore.staff.read(this.props.id);
		this.setState({ userData: data });
	}
	render() {
		const { t, id } = this.props;
		const { userData } = this.state;
		const { isAction, actionPending, fetchPending, getFullName, requestAction, errorPayload } = usersStore.staff;
		const isDataReady = Object.keys(userData).length > 0 && !fetchPending;

		return (
			<View className="m-user-edit">
				<PageTitle contentString={`${t("User edit")} - ${isDataReady ? getFullName(userData).long : "..."}`} />
				<PageHeader back="/users" title={t("User edit")}>
					{
						isDataReady
							? (
								<Fragment>
									<Can perform="users:change-status" use={null}>
										<Button type="alt" onClick={() => requestAction(STAFF_ACTION_CHANGE_STATUS, id, { status: userData.status })}>
											{ userData.status === "active" ? t("Block user") : t("Unblock user") }
										</Button>
									</Can>
									<Can perform="users:delete" use={null}>
										<Button type="alt" onClick={() => requestAction(STAFF_ACTION_DELETE, id)}>{ t("Delete user") }</Button>
									</Can>
								</Fragment>
							)	: null
					}
				</PageHeader>

				<div className="m-user-edit__body">
					<Paper>
						<FormLayout>
							<FormLayoutAside>
								<FormLayoutInfoWrapper>
									<Badge type="primary">{ isDataReady ? userData.status : <PendingIcon />}</Badge>
									<H4 className="m-user-edit__title"> { isDataReady ? getFullName(userData).long : "-- --" } </H4>
									<Paragraph className="m-user-edit__lead">{ t("Created") } { isDataReady ? formatDate({ date: userData.createdAt }) : "--" }  </Paragraph>
								</FormLayoutInfoWrapper>
							</FormLayoutAside>

							<FormLayoutMain>
								{
									isDataReady
										? (
											<SimpleForm
												key="user-edit"
												storedData={userData}
												pending={actionPending && isAction(STAFF_ACTION_UPDATE)}
												config={createUserFormConfig(CREATE_USER_FORM_GRID)}
												onSubmit={this.submitHandler}
												submitLabel={ t("Save changes") }
												onCancel={this.cancelEditHandler}
												onValidate={matchPasswords}
												errors={ errorPayload.validations}
												cancelLabel={ t("Cancel") }
											/>
										)
										: <PendingIcon/>
								}
							</FormLayoutMain>
						</FormLayout>
					</Paper>

				</div>
				<UsersMessages afterChangeStatus={this.afterStatusChanged} afterDelete={this.afterUserDeleted} />
			</View>
		);
	}

	cancelEditHandler = () => {
		this.props.history.goBack();
	};

	afterUserDeleted = () => {
		usersStore.staff.clearActionState();
		this.props.history.push("/users");
	};

	afterStatusChanged = async () => {
		usersStore.staff.clearActionState();
		const data = await usersStore.staff.read(this.props.id);
		this.setState({ userData: data });
	};

	submitHandler = (data: TSimpleFormData) => {
		usersStore.staff.update(this.props.id, data, (nextUserData) => {
			this.setState({ userData: nextUserData });
		});
	};
}

export default withTranslation()(withRouter(observer(UserEdit)));
