import React, {Component, ReactNode} from "react";
import "./styles.scss";
import View from "../../../components/View";
import {
	FormLayout,
	FormLayoutAside,
	FormLayoutBack,
	FormLayoutInfo,
	FormLayoutMain
} from "../../../components/FormLayout";
import Paper from "../../../ui/Paper";
import {Caption} from "../../../ui/Typography";
import {WithTranslation, withTranslation} from "react-i18next";
import SimpleForm from "../../../components/SimpleForm";
import createRequestFormConfig from "./config";
import PopupAlert from "../../../ui/Popup/PopupAlert";
import cardsStore from "../cardsStore";
import { observer } from "mobx-react";
import { withRouter, RouteComponentProps } from "react-router-dom";
import PageTitle from "../../../components/PageTitle";

type Props = {
	children?: ReactNode
} & WithTranslation & RouteComponentProps

class CardCreateRequest extends Component<Props> {

	componentWillUnmount(): void {
		cardsStore.orderForm.clearErrors();
	}

	render() {
		const { t } = this.props;
		const { formId, pending, errors, isFormSuccess, isFormServerError, errorsStatus, post, resetForm } = cardsStore.orderForm;
		return (
			<View className="m-create-card">
				<PageTitle contentString={t("Application for new fuel cards")} />
				<FormLayoutBack to="/cards" />
				<FormLayout>
					<FormLayoutAside>
						<FormLayoutInfo
							icon="card"
							title={ t("Application for new fuel cards") }
							text={ t("I have found some valuable resources for us to use and publisize, all of which are dedicated to responsible travel and care of our environment.") } />
					</FormLayoutAside>
					<FormLayoutMain>
						<Paper>
							<SimpleForm
								key={formId}
								config={createRequestFormConfig}
								pending={pending}
								errors={errors}
								onSubmit={post}
								submitLabel={ t("Apply")}
								storedData={[]} />
							<Caption className="m-create-card__info">
								{ t("After the application is submitted, the manager will call you for a detailed request.") }
							</Caption>
						</Paper>
					</FormLayoutMain>
				</FormLayout>
				{
					isFormSuccess() && (
						<PopupAlert
							onConfirm={this.confirmAlert}
							title={ t("Request sent")}
							description={ t("Out manager will contact you as soon as possible") }
							confirmLabel={t("Close and go to cards")} />
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

	confirmAlert = () => {
		cardsStore.staff.clearActionState();
		this.props.history.push("/cards");
	}
}

export default withTranslation()(withRouter(observer(CardCreateRequest)));
