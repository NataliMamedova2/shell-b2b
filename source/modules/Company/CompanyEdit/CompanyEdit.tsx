import React, {ReactNode, Component} from "react";
import "./styles.scss";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import Paper from "../../../ui/Paper";
import { Paragraph } from "../../../ui/Typography";
import View from "../../../components/View";
import {FormLayout, FormLayoutAside, FormLayoutMain } from "../../../components/FormLayout";
import SimpleForm from "../../../components/SimpleForm";
import Callout from "../../../ui/Callout";
import {withTranslation, WithTranslation} from "react-i18next";
import createCompanyProfileForm from "./config";
import companyStore from "../companyStore";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import { observer } from "mobx-react";
import PageHeader from "../../../components/PageHeader";
import { withRouter, RouteComponentProps } from "react-router-dom";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import profileStore from "../../Users/profileStore";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode
} & WithTranslation & RouteComponentProps

class CompanyEdit extends Component<Props>{

	componentDidMount(): void {
		companyStore.profileForm.get();
	}

	render() {

		const { t } = this.props;
		const {
			pending,
			fetchPending,
			formData,
			errors: profileErrors,
			errorsStatus,
			isFormServerError,
			isFormSuccess,
			formId: profileFormId,
			resetForm
		} = companyStore.profileForm;

		return (
			<View className="m-company-edit">
				<PageTitle contentString={t("Edit general information")} />
				<PageHeader back="/company" title={ t("Edit general information") } />
				<div className="m-company-edit__body">
					<Paper>

						<FormLayout>
							<FormLayoutAside>
								<Paragraph className="m-company-edit__text">
									{ t("For your safety, some of the information is available for change through an administrator request") }
								</Paragraph>
								<Callout>
									{ t("The information will be modified after the request has been processed by the administrator") }
								</Callout>
							</FormLayoutAside>
							<FormLayoutMain>

								{
									fetchPending
										? <PendingIcon/>
										: (
											<SimpleForm
												key={profileFormId}
												storedData={formData}
												pending={pending}
												config={createCompanyProfileForm}
												errors={profileErrors}
												onCancel={this.cancelHandler}
												cancelLabel={ t("Cancel") }
												onSubmit={this.submitHandler}
												submitLabel={ t("Save") }
											/>
										)
								}
							</FormLayoutMain>
						</FormLayout>
					</Paper>
				</div>

				{
					isFormSuccess() && (
						<PopupAlert
							title={ t("Your company profile was updated") }
							confirmLabel={ t("Ok. Close this message") }
							onConfirm={resetForm} />
					)
				}

				{
					(isFormServerError() && errorsStatus) && (
						<PopupAlert
							title={ t("Oops...") }
							description={ "".concat(t("Something went wrong with updating profile."), ` Code: ${errorsStatus}`) }
							confirmLabel={ t("Ok, I will try later") }
							onConfirm={resetForm} />
					)
				}
			</View>
		);
	}

	submitHandler = (data: TSimpleFormData) => {
		companyStore.profileForm.post(data, {
			onSuccess: () => {
				profileStore.updateCompanyName(data.name);
			}
		});
	};

	cancelHandler = () => {
		this.props.history.push("/company");
	};
}

export default withTranslation()(withRouter(observer(CompanyEdit)));
