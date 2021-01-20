import {
	STAFF_ACTION_CHANGE_STATUS,
	STAFF_ACTION_CREATE,
	STAFF_ACTION_DELETE,
	STAFF_ACTION_UPDATE,
	STAFF_STATE_ERROR, STAFF_STATE_SUCCESS
} from "./config";
import {TSimpleFormError} from "@app-types/TSimpleForm";
import {TResponseErrorType} from "../../config/errors";
import {TItemStatus} from "@app-types/TItemStatus";

export type TStaffAction =
	| typeof STAFF_ACTION_CHANGE_STATUS
	| typeof STAFF_ACTION_DELETE
	| typeof STAFF_ACTION_UPDATE
	| typeof STAFF_ACTION_CREATE;

export type TStaffStatus = TItemStatus;

export type TStaffActionState = typeof STAFF_STATE_ERROR | typeof STAFF_STATE_SUCCESS | "pending";

export type TStaffActionPayload = {
	type: TStaffAction,
	id: string
	detail: null | any
};

export type TStaffActionRequest = null | {
	type: TStaffAction,
	state: TStaffActionState
};

export type TStaffFullName = {
	firstName?: string,
	lastName?: string,
	middleName?: string
}

export type TStaffErrorPayload = {
	validations: TSimpleFormError,
	statusCode: TResponseErrorType | ""
}

export type TStaffEndpoint = ((p: string) => string) | string
type TStaffEndpointTypes = "create" | "read" | "update" | "delete" | "changeStatus";

export type TStaffEndpoints = {
	[T in TStaffEndpointTypes]: TStaffEndpoint
};
