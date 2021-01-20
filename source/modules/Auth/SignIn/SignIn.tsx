import React, {Component} from "react";
import { ClearHeader } from "../../../components/Header";
import Text from "../../../ui/Typography/Text";
import {H1, H4, Paragraph} from "../../../ui/Typography";
import Paper from "../../../ui/Paper";
import appAuthStore from "../../../stores/AppAuthStore";
import SimpleForm from "../../../components/SimpleForm";
import { TSimpleFormData } from "@app-types/TSimpleForm";
import { withTranslation, WithTranslation } from "react-i18next";
import createSignInForm from "./config";
import {observer} from "mobx-react";
import ScrollToZero from "../../../ui/ScrollToZero";
import { withRouter, RouteComponentProps } from "react-router-dom";
import classNames from "classnames";
import PageTitle from "../../../components/PageTitle";
import BecomeClientLink from "../BecomeClientLink";

type Props = {
	language: string
} & WithTranslation & RouteComponentProps

type Errors = {
	credentialsErrors: { username: string[] } | null,
	successSent: boolean
}

class SignIn extends Component<Props> {
	state: Errors = {
		credentialsErrors: null,
		successSent: false
	};

	render () {
		const { t } = this.props;

		return (
			<div className="m-sign" style={{ backgroundImage: "url(/media/_temp/sign.jpg)" }}>
				<ScrollToZero />
				<PageTitle contentString={t("Sign In")} />
				<div className="m-sign__header">
					<ClearHeader />
				</div>
				<div className="m-sign__content">
					<div className="m-sign__info">
						<H1 className="m-sign__title" color="light">{ t("Welcome back!")}</H1>
					</div>
					<div className={classNames("m-sign__form", {
						"is-blocked": this.state.successSent
					})}>
						<Paper>
							<H4 className="m-sign__form-title">{ t("Sign In") }</H4>

							{
								this.state.credentialsErrors &&
									this.state.credentialsErrors["username"] &&
									this.state.credentialsErrors["username"][0]  && (
									<Paragraph className="m-sign__error" color="error">{ this.state.credentialsErrors["username"][0] }</Paragraph>
								)
							}

							<SimpleForm
								listenEditing={false}
								config={createSignInForm}
								storedData={{}}
								pending={appAuthStore.loginForm.pending}
								errors={appAuthStore.loginForm.errors}
								onSubmit={this.submitHandler}
								submitLabel={ t("Login") } />

							<div className="m-sign__actions">
								<Text
									to="/auth/restore"
									type="paragraph">{ t("I can't remember my password") }</Text>

								<Paragraph className="m-sign__subtitle">{ t("Not registered yet?") }</Paragraph>

								<BecomeClientLink/>
							</div>
						</Paper>
					</div>
				</div>
			</div>
		);
	}

	gotoSource = () => {
		const params = new URLSearchParams(this.props.location.search);
		const nextUrl = `${window.location.origin}${decodeURIComponent(params.get("from") || `/${this.props.language}`)}`;

		window.location.replace(nextUrl);
	};

	submitHandler = (data: TSimpleFormData) => {
		appAuthStore.logIn(data, {
			onSuccess: ({status, data}) => {
				this.setState({
					credentialsErrors: null
				});
				if(status === 200) {
					localStorage.setItem("__token", data.token);
					this.setState({
						successSent: true
					}, this.gotoSource);

				}
			},
			onFail: (error) => {
				this.setState({
					credentialsErrors: { ...error.data }
				});
			}
		});
	}
}

export default withTranslation()(withRouter(observer(SignIn)));
