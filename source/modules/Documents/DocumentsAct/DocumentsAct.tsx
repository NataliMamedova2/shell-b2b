import React, {Component, ReactNode} from "react";
import "./styles.scss";
import {TSimpleFormConfigFactory, TSimpleFormData} from "@app-types/TSimpleForm";
import View from "../../../components/View";
import { withTranslation, WithTranslation } from "react-i18next";
import {
	FormLayout,
	FormLayoutAside,
	FormLayoutBack,
	FormLayoutInfo,
	FormLayoutMain
} from "../../../components/FormLayout";
import SimpleForm from "../../../components/SimpleForm/SimpleForm";
import Paper from "../../../ui/Paper";
import { withRouter, RouteComponentProps } from "react-router-dom";
import RangeDatepicker from "../../../components/RangeDatepicker";
import PageTitle from "../../../components/PageTitle";
import Callout from "../../../ui/Callout";
import Button from "../../../ui/Button";
import ServerErrorMessage from "../../../components/ServerErrorMessage";
import documentsStore from "../documentsStore";
import {format} from "date-fns";
import {observer} from "mobx-react";
import DownloadCreatedFile from "../DownloadCreatedFile";
import {TFile} from "../types";

type Props = {
	children?: ReactNode
} & WithTranslation & RouteComponentProps;

type State = {
	file?: TFile
}

const MIN_DATE = new Date("2020-01-01").setHours(0,0,0,0);

const createDocumentActForm: TSimpleFormConfigFactory =  (translate) => ([{
	title: translate("Select of the period"),
	titleStyle: "h4",
	fields: [
		{
			type: RangeDatepicker,
			key: "dates",
			label: "",
			defaultValue: {},
			options: {
				labelAs: "span",
				monthsOnly: true,
				disableMobile: true,
				fixedMinDate: MIN_DATE,
			},
		}
	],
	grid: [
		["dates"],
	]
}]);


class DocumentsAct extends Component<Props, State> {
	state: State = { };
	generateActRules = this.props.t("You can form a reconciliation report from 2020");

	componentWillUnmount(): void {
		documentsStore.actRequest.clear();
	}

	render () {
		const { t } = this.props;
		const {pending, formId, isFormSuccess, isFormServerError, clear} = documentsStore.actRequest;

		return (
			<View className="m-request-act">
				<PageTitle contentString={t("Request for a reconciliation act")} />
				<FormLayoutBack to="/documents" />
				<FormLayout>
					<FormLayoutAside>
						<FormLayoutInfo
							icon="doc"
							title={ t("Request for a reconciliation act") }
							text={t("Request for a reconciliation act [description]") } />
					</FormLayoutAside>
					<FormLayoutMain>
						<Paper>
							<SimpleForm
								key={formId}
								before={<Callout type="inform">{ this.generateActRules }</Callout>}
								config={createDocumentActForm}
								onSubmit={this.submitHandler}
								submitLabel={ t("Submit request") }
								pending={pending}
							/>

							<div className="m-request-act__footer">
								<Callout type="inform" icon="feedback">
									{ t("Please contact through the feedback form if you need a report for a period before 2020") }
								</Callout>
								<Button type="alt" to="/feedback?category=financial-issue" disabled={pending}>
									{ t("Ask a report") }
								</Button>
							</div>
						</Paper>
					</FormLayoutMain>
				</FormLayout>

				{
					isFormSuccess() && this.state.file
						? <DownloadCreatedFile
							title={this.props.t("Act created")}
							file={ this.state.file }
							afterLoad={this.clearFile} />
						: null
				}
				<ServerErrorMessage when={isFormServerError()} onClose={clear}/>
			</View>
		);
	}

	submitHandler = (formData : TSimpleFormData) => {
		const preparedData = this.prepareInvoiceData(formData);

		documentsStore.actRequest.post(preparedData, {
			onSuccess: res => this.setFile(res.data)
		});
	};

	setFile = (file: TFile) => {
		this.setState(() => ({
			file
		}));
	};

	clearFile = () => {
		this.setState(() => ({
			file: undefined
		}), documentsStore.actRequest.clear);
	};

	prepareInvoiceData = (data: TSimpleFormData) => {
		const {startDate, endDate} = data.dates;
		const dateFromString = startDate || Date.now();
		const dateToString = endDate || dateFromString;

		return {
			dateFrom: format(dateFromString, "yyyy-MM"),
			dateTo: format(dateToString, "yyyy-MM"),
		};
	}
}

export default withTranslation()(withRouter(observer(DocumentsAct)));
