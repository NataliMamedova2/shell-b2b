import React, {ReactNode, Component, Fragment} from "react";
import View from "../../../components/View";
import {FormLayout, FormLayoutAside, FormLayoutInfoWrapper, FormLayoutMain} from "../../../components/FormLayout";
import Paper from "../../../ui/Paper";
import SimpleForm from "../../../components/SimpleForm";
import {  H4, Paragraph } from "../../../ui/Typography";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import {
	createUserFormConfig,
	EDIT_PROFILE_FORM_GRID,
	matchPasswords,
} from "../config";
import Badge from "../../../ui/Badge";
import {formatDate} from "../../../libs";
import {observer} from "mobx-react";
import { withTranslation, WithTranslation } from "react-i18next";
import { withRouter, RouteComponentProps } from "react-router-dom";
import PageHeader from "../../../components/PageHeader";
import profileStore from "../profileStore";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode,
} & WithTranslation & RouteComponentProps

type State = {
	error: boolean,
	errors: { [key: string]: string[] }
	success: boolean,
	pending: boolean
}

class UserMe extends Component<Props> {

	state: State = {
		errors: {},
		error: false,
		success: false,
		pending: false,
	};

	render() {
		const { t } = this.props;

		return (
			<View className="m-user-edit">
				<PageTitle contentString={t("My profile")} />
				<PageHeader title={ t("My profile") } />

				<div className="m-user-edit__body">
					<Paper>
						<FormLayout>
							<FormLayoutAside>
								<FormLayoutInfoWrapper>

									<Badge type="primary">{ profileStore.pending ? <PendingIcon /> : t("Active (me)")}</Badge>
									{
										profileStore.pending
											? <PendingIcon />
											: (
											<Fragment>
												<H4 className="m-user-edit__title">{ profileStore.fullName }</H4>
												<Paragraph className="m-user-edit__lead">{ t("Created") } { formatDate({ date: profileStore.me.createdAt }) }  </Paragraph>
											</Fragment>
										)
									}
								</FormLayoutInfoWrapper>
							</FormLayoutAside>

							<FormLayoutMain>

								{
									profileStore.pending
										? <PendingIcon/>
										: (
											<SimpleForm
												key="user-edit"
												errors={this.state.errors}
												storedData={profileStore.me}
												pending={this.state.pending}
												config={createUserFormConfig(EDIT_PROFILE_FORM_GRID)}
												onSubmit={this.submitHandler}
												submitLabel={ t("Save changes") }
												onValidate={matchPasswords}
											/>
										)
								}
							</FormLayoutMain>
						</FormLayout>
					</Paper>

				</div>
				{
					this.state.success && (
						<PopupAlert
							title={t("Profile was updated!")}
							description={t("The changes will take effect after the administrator has processed them")}
							confirmLabel={t("Ok")}
							onConfirm={this.hideMessage} />
					)
				}

				{
					this.state.error && (
						<PopupAlert
							title={t("Oops!")}
							description={t("Something went wrong with updating your profile")}
							confirmLabel={t("Ok")}
							onConfirm={this.hideMessage} />
					)
				}
			</View>
		);
	}


	hideMessage = () => {
		this.setState({ success: false, error: false });
	};

	cancelHandler = () => {
		this.props.history.push("/users");
	};

	submitHandler = async (data: TSimpleFormData) => {

		const { rePassword, password, ...rest  } = data;

		const preparedData = { ...rest };

		if(password) {
			preparedData.password = password;
		}

		this.setState({ pending: true });
		try {

			await profileStore.updateMe(preparedData);
			this.setState({ success: true, errors: {}, pending: false});

		} catch (e) {
			const { status, data } = e.response;
			if( status === 400 ) {
				this.setState({ errors: data.errors, pending: false});
			}

			if( status !== 400) {
				this.setState({ error: true, pending: false});
			}
		}
	};
}

export default withTranslation()(withRouter(observer(UserMe)));
