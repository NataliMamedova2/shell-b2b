import {observable, decorate, runInAction, action} from "mobx";
import ListStore from "../../stores/ListStore";
import {get} from "../../libs";
import FormStore from "../../stores/FormStore";
import { TDocumentType, TDocumentStatus} from "../../config/dictionary";

export type TSupplier = {
	readonly id: string,
	readonly name: string,
	readonly price: number
}

export type TSupplierOption = { [key: string]: TSupplier["name"] }
export type TSupplierPrice = { [key: string]: TSupplier["price"] }

type TDocumentItem = {
	number: string,
	amount: string,
	type: keyof TDocumentType,
	status: keyof TDocumentStatus,
	createdAt: Date,
	file: {
		name: string,
		link: string
	}
}


class DocumentsStore {
	list = new ListStore<TDocumentItem>("/documents",{
		page: "1",
		sort: "createdAt",
		order: "desc"
	});

	customInvoice = new FormStore("/invoice/amount");
	suppliesInvoice = new FormStore("/invoice/supplies");
	actRequest = new FormStore("/documents/act-checking");

	debtAmount: number = 0;
	debtPending: boolean = false;
	sotaToken: string|null = null;
	debtUpdate = () => {
		this.debtPending = true;

		get<{ amount: number }>({ endpoint: "/invoice/credit-debt"})
			.then((res) =>{
				runInAction(() => {

					this.debtAmount = res.data.amount;
					this.debtPending = false;
				});
			});
	};


	suppliersPending: boolean = false;
	getSuppliers = (onSuccess: (l: TSupplier[]) => void) => {
		this.suppliersPending = true;

		get<TSupplier[]>({ endpoint: "/supplies"})
			.then((res) =>{
				runInAction(() => {
					this.suppliersPending = false;
					onSuccess(res.data);
				});
			});
	}

	sotaTokenPending: boolean = false;
	getSotaToken = () => {
		this.sotaTokenPending = true;

		if(this.sotaToken) {
			this.sotaTokenPending = false;
		} else  {
			get<{ token: string }>({ endpoint: "/sota"})
				.then((res) =>{
					runInAction(() => {
						this.sotaTokenPending = false;
						this.sotaToken = res.data?.token ?? null;
					});
				});
		}

	};
}

decorate(DocumentsStore, {
	list: observable,
	debtAmount: observable,
	debtPending: observable,
	debtUpdate: action,
	suppliersPending: observable,
	getSuppliers: action,
	sotaTokenPending: observable,
	sotaToken: observable,
	getSotaToken: action
});

const documentsStore = new DocumentsStore();

export default documentsStore;
