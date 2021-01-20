import React, {Component, ReactNode} from "react";
import "./styles.scss";
import Paper from "../../../ui/Paper";
import {TSimpleForm, TSimpleFormData} from "@app-types/TSimpleForm";
import SimpleForm from "../../../components/SimpleForm";
import { RouteComponentProps, withRouter } from "react-router-dom";
import { withTranslation, WithTranslation } from "react-i18next";
import InvoiceByDebtField from "../InvoiceByDebtField";
import documentsStore from "../documentsStore";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import {observer} from "mobx-react";
import {toJS} from "mobx";
import ServerErrorMessage from "../../../components/ServerErrorMessage";
import InvoicePageLayout from "../InvoicePageLayout";
import InvoiceLargeLink from "../InvoiceLargeLink";
import {TFunction} from "i18next";
import {toPrice} from "../../../libs";
import PageTitle from "../../../components/PageTitle";
import DownloadCreatedFile from "../DownloadCreatedFile";
import {TFile} from "../types";

type Props = {
	children?: ReactNode
} & RouteComponentProps & WithTranslation;

type State = {
	file?: TFile
}

const configureCustomBillField = (debtAmount: number) => (translate: TFunction): TSimpleForm => {
	const noDebit = debtAmount <= 0;
	const fieldOptions = noDebit ? { hideDebtInput: true } : {};

	return [{
		fields: [{
			type: InvoiceByDebtField,
			key: "amount",
			label: translate("Please select a payment amount"),
			options: {
				...fieldOptions,
				errorPreview: translate("Wrong amount")
			},
			defaultValue: {
				type: noDebit ? "custom" : "credit",
				amount: 0,
				creditAmount: debtAmount
			},
		}],
		grid: [
			["amount"]
		]
	}];
};

class RequestDebtInvoice extends Component<Props> {
	state: State = {
	};

	componentDidMount(): void {
		documentsStore.debtUpdate();
	}

	componentWillUnmount(): void {
		documentsStore.customInvoice.clear();
	}

	render() {

		const {t} = this.props;
		const {debtPending, debtAmount} = documentsStore;
		const {pending, errors, formId, isFormSuccess, isFormServerError} = documentsStore.customInvoice;

		return (
			<InvoicePageLayout className="m-bill-create-custom" to="/documents/invoice">
				<PageTitle contentString={t("Manually pay this amount")} />
				<InvoiceLargeLink icon="check-square" title={t("Manually pay this amount")}/>
				<Paper>
					{
						debtPending
							? <PendingIcon/>
							: (
								<SimpleForm
									key={formId}
									config={configureCustomBillField(debtAmount)}
									storedData={{}}
									pending={pending}
									errors={toJS(errors)}
									submitLabel={t("To form")}
									onSubmit={this.submitHandler}
									cancelLabel={t("Cancel")}
									listenEditing={false}
									onCancel={this.toDocuments}
								/>
							)
					}

				</Paper>

				{
					isFormSuccess() && this.state.file
						? <DownloadCreatedFile
							title={this.props.t("Invoice created")}
							file={ this.state.file }
							afterLoad={this.clearFile} />
						: null
				}
				<ServerErrorMessage when={isFormServerError()} onClose={documentsStore.customInvoice.clear}/>
			</InvoicePageLayout>
		);
	}

	submitHandler = (data : TSimpleFormData) => {
		const preparedData = this.prepareInvoiceData(data);

		documentsStore.customInvoice.post(preparedData, {
			onSuccess: ({data}) => {
				this.setState(() => ({ file: {...data} }));
			}
		});
	};

	toDocuments = () => this.props.history.push("/documents/invoice");

	prepareInvoiceData = ({amount}: TSimpleFormData) => ({
		amount: amount.type === "custom" ? amount.amount : toPrice(amount.creditAmount)
	});

	clearFile = () => {
		this.setState(() => ({
			file: undefined
		}), documentsStore.customInvoice.clear);
	};
}

export default withTranslation()(withRouter(observer(RequestDebtInvoice)));
