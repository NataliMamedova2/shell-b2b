import {TAccessRole} from "@app-types/TAccessRole";

export type TUser = {
	firstName?: string,
	lastName?: string,
	role?: TAccessRole
};
