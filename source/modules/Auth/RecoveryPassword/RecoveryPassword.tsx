import React, { Component } from "react";
import {ClearHeader} from "../../../components/Header";
import {H1, H4, Paragraph} from "../../../ui/Typography";
import Paper from "../../../ui/Paper";
import appAuthStore from "../../../stores/AppAuthStore";
import SimpleForm from "../../../components/SimpleForm";
import { TSimpleFormData } from "@app-types/TSimpleForm";
import {RouteComponentProps, withRouter} from "react-router-dom";
import { withTranslation, WithTranslation } from "react-i18next";
import {observer} from "mobx-react";
import createRestorePasswordForm from "./config";
import ScrollToZero from "../../../ui/ScrollToZero";
import PageTitle from "../../../components/PageTitle";
import BecomeClientLink from "../BecomeClientLink";

type Props = RouteComponentProps & WithTranslation;

class RecoveryPassword extends Component<Props>{

	render () {

		const { t } = this.props;

		return (
			<div className="m-sign m-sign--yellow">
				<ScrollToZero />
				<PageTitle contentString={t("Recovery password")} />
				<div className="m-sign__header">
					<ClearHeader/>
				</div>
				<div className="m-sign__content">
					<div className="m-sign__info">
						<H1 className="m-sign__title">{ t("Dont worry!") }</H1>
						<Paragraph className="m-sign__lead">
							{ t( "Add your email address to form - we send the email with the new password or a link to the recovery password page.") }
						</Paragraph>
					</div>
					<div className="m-sign__form">
						<Paper>
							<H4 className="m-sign__form-title">{ t("Recovery password") }</H4>

							<SimpleForm
								listenEditing={false}
								config={createRestorePasswordForm}
								errors={appAuthStore.recoveryForm.errors}
								pending={appAuthStore.recoveryForm.pending}
								onSubmit={this.submitHandler}
								submitLabel={ t("Recover password") } />

							<div className="m-sign__actions">
								<Paragraph className="m-sign__subtitle">{ t("Not registered yet?") }</Paragraph>
								<BecomeClientLink/>
							</div>

						</Paper>
					</div>
				</div>
			</div>
		);
	}

	submitHandler = (data: TSimpleFormData) => {
		appAuthStore.submitRecovery(data, {
			onSuccess: () => {
				this.props.history.push("/auth/restore-message");
			},
			onFail: () => {},
		});
	}
}

export default withTranslation()(withRouter(observer(RecoveryPassword)));
