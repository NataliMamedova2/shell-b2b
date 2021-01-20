import React, {Component, ReactNode} from "react";
import "./styles.scss";
import Paper from "../../../ui/Paper";
import SimpleForm from "../../../components/SimpleForm";
import {TSimpleForm, TSimpleFormConfigFactory, TSimpleFormData} from "@app-types/TSimpleForm";
import {withTranslation, WithTranslation} from "react-i18next";
import {withRouter, RouteComponentProps} from "react-router";
import {printFormattedSum} from "../../../libs";
import InvoiceBySuppliersField from "../InvoiceBySuppliersField";
import documentsStore, {TSupplier, TSupplierOption, TSupplierPrice} from "../documentsStore";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import {toJS} from "mobx";
import {observer} from "mobx-react";
import ServerErrorMessage from "../../../components/ServerErrorMessage";
import InvoiceLargeLink from "../InvoiceLargeLink";
import InvoicePageLayout from "../InvoicePageLayout";
import {TFunction} from "i18next";
import PageTitle from "../../../components/PageTitle";
import {TFile} from "../types";
import DownloadCreatedFile from "../DownloadCreatedFile";

type Props = {
	children?: ReactNode
} & RouteComponentProps & WithTranslation

type State = {
	suppliers: TSupplier[],
	selected: string[],
	formConfig?: TSimpleFormConfigFactory,
	file?: TFile
}


const configureCalculateInvoiceForm = (suppliers: TSupplier[], selectedIds: string[] ) => (translate: TFunction): TSimpleForm => {

	const prices: TSupplierPrice = suppliers.reduce((acc: TSupplierPrice, current: TSupplier): TSupplierPrice => {
		acc[current.id as string] = current.price;
		return acc;
	}, {});

	const arrayOptions: TSupplierOption = suppliers.reduce((acc: TSupplierOption, current: TSupplier): TSupplierOption => {
		if(!selectedIds.includes(current.id)) {
			acc[current.id as string] = current.name;
		}
		return acc;
	}, {});


	return [{
		fields: [{
			key: "items",
			label: "",
			type: "Array",
			options: {
				arrayOptions: {
					type: InvoiceBySuppliersField,
					defaultValue: {},
					buttonLabel: translate("Add Inventory"),
					removeFrom: 0,
					removeIcon: "close",
					maxCount: suppliers.length,
				},
				defaultLabel: translate("Select type of fuel"),
				selectOptions: arrayOptions,
				prices: prices,
			},
			defaultValue: []
		}],
		grid: [["items"]]
	}];

};

class RequestSuppliersInvoice extends Component<Props, State> {
	state: State = {
		suppliers: [],
		selected: [],
		formConfig: configureCalculateInvoiceForm([], []),
	};

	componentDidMount(): void {
		documentsStore.getSuppliers(data => {
			this.setState(() => ({
				suppliers: toJS(data),
				formConfig: configureCalculateInvoiceForm(toJS(data), this.state.selected)
			}));
		});
	}

	componentWillUnmount(): void {
		documentsStore.suppliesInvoice.clear();
	}

	render () {
		const { t } = this.props;
		const { pending, errors, isFormSuccess, isFormServerError } = documentsStore.suppliesInvoice;

		return (
			<InvoicePageLayout className="m-bill-create-calc" to="/documents/invoice">
				<PageTitle contentString={t("Volume payment calculation")} />
				<InvoiceLargeLink icon="doc" title={t("Volume payment calculation")} />
				<Paper>

					{
						documentsStore.suppliersPending
							? <PendingIcon/>
							: (
								<SimpleForm
									config={this.state.formConfig!}
									pending={pending}
									errors={errors}
									storedData={{}}

									onChange={this.changeHandler}

									submitLabel={t("Create an invoice")}
									onSubmit={this.submitHandler}

									cancelLabel={t("Cancel")}
									onCancel={this.toDocuments}
									score={this.calcScore}

									scoreLabel={t("Total sum")}
									scrollToErrorSelector=".c-fuel-input.is-error"
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
				<ServerErrorMessage when={isFormServerError()} onClose={this.toDocuments} />
			</InvoicePageLayout>
		);
	}

	toDocuments = () => this.props.history.push("/documents/invoice");
	toDocumentsList = () => this.props.history.push("/documents");

	submitHandler = (data : TSimpleFormData) => {
		const preparedData = {
			items: data.items.map((item: any) => ({ id: item.id, volume: item.volume }))
		};

		documentsStore.suppliesInvoice.post(preparedData, {
			onSuccess: ({data}) => {
				this.setState(() => ({ file: {...data} }));
			}
		});
	};

	changeHandler = (data : TSimpleFormData) => {
		const selected = data.items.map((item: any) => item.id).filter(Boolean);

		if(selected.length !== this.state.selected.length) {
			this.setState(() => ({
				selected,
				formConfig: configureCalculateInvoiceForm(this.state.suppliers, selected)
			}));
		}
	};

	calcScore = ({ items }: TSimpleFormData): string | number => {
		const value = items.reduce((acc: any, current: any): number => {
			if(!current.meta) {
				return acc;
			}
			acc += (current.meta.price * current.volume) || 0;
			return acc;
		}, 0);

		return printFormattedSum(value);
	};

	clearFile = () => {
		this.setState(() => ({
			file: undefined
		}), documentsStore.suppliesInvoice.clear);
	};
}

export default withTranslation()(withRouter(observer(RequestSuppliersInvoice)));
