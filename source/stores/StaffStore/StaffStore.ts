import {observable, decorate, action} from "mobx";
import {TSimpleFormData, TSimpleFormError} from "@app-types/TSimpleForm";
import {
	TStaffActionState,
	TStaffActionPayload,
	TStaffStatus,
	TStaffAction,
	TStaffFullName,
	TStaffActionRequest,
	TStaffEndpoints, TStaffErrorPayload
} from "./types";
import { STAFF_ACTION_CREATE, STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_UPDATE, STAFF_ACTION_DELETE, STAFF_STATE_ERROR, STAFF_STATE_SUCCESS } from "./config";
import staffActionFactory from "./staffActionFactory";
import { get } from "../../libs";
import {getErrorType} from "../../config/errors";
import uuid4 from "uuid/v4";

/**
 * Utils for cover actions with profile-like entity. For example, drivers, users, cards.
 * Cover cases:
 *  - load data of certain profile
 *  - changeStatus of profile
 *  - create/update/delete a profile
 *  Every action send request to server.
 *  Include utils methods for combine or compute profile info: getFullName, eg
 *  Have observable properties for reflect on UI: fetchPending, actionPending, actionState, eg
 */
class StaffStore {
	/**
	 * { boolean } fetchPending
	 * What: Indicate progress state for load info.
	 * Why: Represent the progress of loading and can be reflected in a user interface.
	 */
	fetchPending: boolean = false;
	silentPending: boolean = false;

	staffFormId: string = uuid4();
	resetStaffFormId = () => (this.staffFormId = uuid4());

	/**
	 * { {type, id?} | null } actionPayload
	 * What: It's a store for `action type` and `id` of staff.
	 * Why: Useful for cases when the user must confirm the action. There is stored information which will use in `a Confirm` component.
	 * Note: Value `null` means is no action right now
	 */
	actionPayload: TStaffActionPayload | null = null;

	/**
	 * { {type, state} | null } actionState
	 * What: Represent the state of the current action.
	 * Why: For display information about an error or a success in a page.
	 * Note: Value `null` means the previous action is completed and is not any action right now.
	 */
	actionState: TStaffActionRequest = null;
	/**
	 * { boolean } actionPending
	 * What:
	 * Why:
	 */
	actionPending: boolean = false;
	/**
	 * Errors list
	 */
	errorPayload: TStaffErrorPayload  = { validations: {}, statusCode: "" };
	/**
	 * endpoints - set of action endpoints.
	 */
	constructor(private endpoints: TStaffEndpoints) {}

	/**
	 * Set `actionPayload` info which can be used for Confirm action
	 */
	requestAction = (type: TStaffAction, id: string, detail: null | any = null ) => {
		this.actionPayload = { type, id, detail };
	};
	/**
	 * Clear `actionPayload`. In cases: action completed or canceled
	 */
	clearAction = () => {
		this.actionPayload = null;
	};

	setErrorsPayload = (payload: { validations: TSimpleFormError, statusCode: number } | null) => {
		if(payload === null) {
			this.errorPayload = {
				validations: {},
				statusCode: ""
			};
			return;
		}
		const errorCode = getErrorType(payload.statusCode);

		this.errorPayload = {
			validations: payload.validations,
			statusCode: errorCode
		};
	};

	clearErrorsPayload = () => this.setErrorsPayload(null);

	/**
	 * Request to action endpoint was sent. Waiting response
	 */
	setActionPending = (val: boolean) => {
		this.actionPending = val;
	};

	/**
	 * Response of action is here. Can be success or error. Update `actionState` for update UI
	 */
	setActionState = (type: TStaffAction, state: TStaffActionState) => {
		this.actionState = { type, state };
	};

	/**
	 * Action is completed. Clear `actionState` for noConflict with the same action for another staff or  other actions
	 */
	clearActionState = () => {
		this.actionState = null;
	};

	/**
	 * Is `type` of action right now in progress?
	 */
	isAction = (type: TStaffAction): boolean => {
		return !!this.actionPayload && this.actionPayload.type === type;
	};

	/**
	 * Is `type` and `state` of action right now?
	 */
	isActionState = (type: TStaffAction, state: TStaffActionState) => {
		return !this.actionPending && this.actionState && this.actionState.type === type && this.actionState.state === state;
	};

	/**
	 * Is `type` of action finished with Error?
	 * Note: `*` means action-typeless. In other words: some action has finished with error
	 */
	isActionError = (type: TStaffAction | "*") => {
		if(type === "*") {
			return !this.actionPending && this.actionState && this.actionState.state === STAFF_STATE_ERROR;
		}
		return this.isActionState(type, STAFF_STATE_ERROR);
	};

	/**
	 * Is `type` of action finished with Success?
	 */
	isActionSuccess = (type: TStaffAction) => {
		return this.isActionState(type, STAFF_STATE_SUCCESS);
	};

	/**
	 * Update value fetchPending.
	 * Note: before request begin - true, after request was finished - false
	 */
	setFetchPending = (val: boolean, silent?: boolean) => {
		if(silent) {
			this.silentPending = val;
		} else  {
			this.fetchPending = val;
		}
	};

	/**
	 * What: Configure staffActionFactory. Pass StaffStore method into separated function
	 * Why: for consistent to DRY, for reduce typo errors and less lines code
	 * Note: methods passed into `staffActionFactory` must be defined above it called
	 */
	staffAction = staffActionFactory({
		setActionPending: this.setActionPending,
		setActionState: this.setActionState,
		clearAction: this.clearAction
	});

	/**
	 * Change status of staff
	 */
	changeStatus = (status: TStaffStatus, onSuccess?: () => void) => {
		if(!this.actionPayload) { return false; }
		const url = this.createUrl("changeStatus", this.actionPayload.id);

		this.staffAction({
			action: STAFF_ACTION_CHANGE_STATUS,
			url: url,
			data: { status },
			onSuccess: onSuccess
		});
	};

	/**
	 * Delete staff
	 */
	delete = () => {
		if(!this.actionPayload) { return false; }

		this.staffAction({
			action: STAFF_ACTION_DELETE,
			url: this.createUrl("delete", this.actionPayload.id)
		});
	};

	/**
	 * Delete information about staff
	 */
	update = (id: string, data: TSimpleFormData, onSuccess?: (d: TSimpleFormData) => void) => {
		this.setActionPending(true);
		this.requestAction(STAFF_ACTION_UPDATE, id);

		this.staffAction({
			action: STAFF_ACTION_UPDATE,
			url: this.createUrl("update", id),
			data: data,
			onSuccess: (updatedData) => {
				this.clearErrorsPayload();
				if(onSuccess) {
					onSuccess(updatedData);
				}
			},
			onFail: (errorResponse) => this.setErrorsPayload({ validations: errorResponse.data.errors, statusCode: errorResponse.status })
		});
	};

	/**
	 * Create staff
	 */
	create = (data: TSimpleFormData, successCallback?: ((data: any) => void)) => {
		this.staffAction({
			action: STAFF_ACTION_CREATE,
			url: this.createUrl("create"),
			data: data,
			onSuccess: data =>  {
				if(successCallback) successCallback(data);
				this.clearErrorsPayload();
			},
			onFail: (errorResponse) => this.setErrorsPayload({ validations: errorResponse.data.errors, statusCode: errorResponse.status })
		});
	};

	/**
	 * Read (or fetch) staff information by Id.
	 * FIXME Are there any reason to update or refactor this method? Less code? DRY?
	 */
	read = (id: string, silent?: boolean): Promise<{ data: TSimpleFormData }> => {

		this.setFetchPending(true, silent);

		return new Promise(async (resolve) => {
			try {
				const res = await get({endpoint: this.createUrl("read", id)});
				this.setFetchPending(false, silent);
				resolve(res.data);
			} catch (res) {
				this.setFetchPending(false, silent);
			}
		});
	};
	/**
	 * Get info and return fullName of staff in 2 modes: short (Ivanov PP) and long (Ivanov Petro Petrovych);
	 */
	getFullName = <T extends TStaffFullName>(item: T) => {
		const { firstName = "", lastName = "", middleName = "" }: T = item;

		const short = `${lastName} ${firstName[0].toUpperCase()}${middleName ? middleName[0].toUpperCase(): ""}`;

		const long = `${lastName} ${firstName} ${middleName ? middleName : ""}`;
		return { short, long };
	};

	/**
	 * What: Create url for action request
	 * Why: DRY and reduce typo errors.
	 */
	private createUrl = (type: keyof TStaffEndpoints, id?: string): string => {
		const endpoint = this.endpoints[type];

		if(typeof endpoint === "function" && id) {
			return endpoint(id);
		}
		if(id) {
			return `${this.endpoints[type]}/${id}`;
		}
		return `${this.endpoints[type]}`;
	}
}

decorate(StaffStore, {

	staffFormId: observable,
	resetStaffFormId: action,
	/** Read */
	fetchPending: observable,
	setFetchPending: action,
	/**  Create/update/delete */
	staffAction: action,
	actionPayload: observable,
	actionPending: observable,
	actionState: observable,
	errorPayload: observable,
	requestAction: action,
	clearAction: action,
	clearActionState: action,
	setActionPending: action,
	setActionState: action,
	setErrorsPayload: action
});

export default StaffStore;
