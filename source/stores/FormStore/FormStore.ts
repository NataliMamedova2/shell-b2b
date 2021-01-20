import {action, decorate, observable} from "mobx";
import {TSimpleFormData, TSimpleFormError} from "@app-types/TSimpleForm";
import {post as postRequestWithAuth, get as getRequestWithAuth, logger} from "../../libs";
import uuid4 from "uuid/v4";
import {AxiosResponse} from "axios";

const FORM_STATE_UNTOUCHED = "form-hold";
const FORM_STATE_SUCCESS = "form-success";
const FORM_STATE_ERROR = "form-error";

type FORM_STATE = typeof FORM_STATE_UNTOUCHED | typeof FORM_STATE_ERROR | typeof FORM_STATE_SUCCESS;

type TFormRequestOn = {
	onSuccess?: (res: AxiosResponse) => void,
	onFail?: (error: any) => void,
}

class FormStore {
	pending: boolean = false;
	fetchPending: boolean = false;
	errors: TSimpleFormError = {};
	errorsStatus: number | null = null;
	state: FORM_STATE = FORM_STATE_UNTOUCHED;
	formData: TSimpleFormData = {};
	formId: string = uuid4();

	constructor(private setEndpoint: string, private getEndpoint?: string | null) {}

	/**
	 * Send form data
	 * @param data,
	 * @param on - callback onSuccess or onFail for handle these cases
	 */
	post = (data: TSimpleFormData, on?: TFormRequestOn) => {
		this.setPending(true);

		postRequestWithAuth({ endpoint: this.setEndpoint, data })
			.then((res: AxiosResponse) => {
				this.setState(FORM_STATE_SUCCESS);
				this.setErrors({}, null);
				this.setFormData(res.data);

				if(on && on.onSuccess) {
					on.onSuccess(res);
				}

			})
			.catch((errors) => {
				logger("/POST Form store error", errors);
				this.setErrors(errors.data.errors, errors.status);
				this.setState(FORM_STATE_ERROR);

				if(on && on.onFail) {
					on.onFail(errors);
				}
			})
			.finally(() => this.setPending(false));
	};

	/**
	 * For get initial form data
	 */
	get = (on?: TFormRequestOn) => {
		if(!this.getEndpoint) return false;
		this.setFetchPending(true);

		getRequestWithAuth({ endpoint: this.getEndpoint })
			.then((res: AxiosResponse) => {
				this.setFormData(res.data);

				if(on && on.onSuccess) {
					on.onSuccess(res);
				}
			})
			.catch((errors) => {
				logger("/GET Form store error", errors);
				this.setErrors(errors.data.errors);
				if(on && on.onFail) {
					on.onFail(errors);
				}
			})
			.finally(() => this.setFetchPending(false));
	};

	setErrors = (errors: TSimpleFormError, statusCode: number | null = null) => {
		this.errors = errors;
		if(statusCode){
			this.errorsStatus = statusCode;
		}
	};

	clearErrors = () => {
		this.setErrors({});
		this.errorsStatus = null;
	};

	clear = () => {
		this.resetForm();
		this.clearErrors();
	};

	setPending = (val: boolean) => {
		this.pending = val;
	};

	setFetchPending = (val: boolean) => {
		this.fetchPending = val;
	};

	setState = (newState: FORM_STATE) => {
		this.state = newState;
	};

	setFormData = (data: TSimpleFormData) => {
		this.formData = data;
	};

	resetForm = () => {
		this.setState(FORM_STATE_UNTOUCHED);
		this.updateFormId();
	};

	updateFormId = () => {
		this.formId = uuid4();
	};

	isFormSuccess = () => {
		return !this.pending && this.state === FORM_STATE_SUCCESS;
	};
	isFormServerError = (): boolean => {
		const serverErrors = [404, 500];

		return !this.pending && (this.state === FORM_STATE_ERROR && (!!this.errorsStatus && serverErrors.includes(this.errorsStatus)));
	};
}

decorate(FormStore, {
	pending: observable,
	fetchPending: observable,
	errors: observable,
	errorsStatus: observable,
	state: observable,
	formId: observable,
	formData: observable,
	post: action,
	setErrors: action,
	setPending: action,
	setState: action,
	updateFormId: action
});

export default FormStore;
