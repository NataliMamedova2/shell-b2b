import React, { Component } from "react";
import "./styles.scss";
import Can from "../../components/Can";
import View from "../../components/View";
import { H1, Paragraph } from "../../ui/Typography";
import SimpleForm from "../../components/SimpleForm";
import PageIcon from "../../components/PageIcon";
import PopupAlert from "../../ui/Popup/PopupAlert";
import { withTranslation, WithTranslation } from "react-i18next";
import Paper from "../../ui/Paper";
import createFeedbackForm from "./config";
import feedbackStore from "./feedbackStore";
import {observer} from "mobx-react";
import PageTitle from "../../components/PageTitle";
import { withRouter, RouteComponentProps } from "react-router-dom";
import QueryController from "../../stores/ListStore/QueryController";
type Props = {} & WithTranslation & RouteComponentProps;

class Feedback extends Component<Props> {

	componentWillUnmount(): void {
		feedbackStore.form.clearErrors();
	}

	render() {
		const { t, location: { search } } = this.props;
		const { formId, isFormSuccess, isFormServerError, errorsStatus, post, resetForm } = feedbackStore.form;

		const categoryFromParams = QueryController.getParamsFromSearch(search, [])["category"];
		const predefinedData = categoryFromParams ? { "category": categoryFromParams } : {};

		return (
			<Can perform="feedback:main">
				<View className="m-feedback">
					<PageTitle contentString={ t("Feedback") } />
					<div className="m-feedback__header">
						<PageIcon type="comment"/>
						<H1 className="m-feedback__title">{t("Feedback form")}</H1>
						<Paragraph>{t("I have found some valuable resources for us to use and publisize, all of which are dedicated to responsible travel and care of our environment.")}</Paragraph>
					</div>

					<div className="m-feedback__body">
						<Paper>
							<SimpleForm
								key={formId}
								config={createFeedbackForm}
								pending={feedbackStore.form.pending}
								errors={feedbackStore.form.errors}
								storedData={predefinedData}
								onSubmit={post}
								submitLabel={t("Submit feedback")} />
						</Paper>
					</div>

					{
						isFormSuccess() && (
							<PopupAlert
								title={t("Your feedback Sent")}
								description={t("We will answer you as soon as possible!")}
								confirmLabel={t("Ok")}
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
			</Can>
		);
	}
}

export default withTranslation()(withRouter(observer(Feedback)));
