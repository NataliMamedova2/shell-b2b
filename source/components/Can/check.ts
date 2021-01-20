import {TRbacRulesConfig} from "../../config/rbac-rules";
import {TAccessRole} from "@app-types/TAccessRole";

type Props = {
	rules: TRbacRulesConfig,
	role: TAccessRole,
	action: string,
	data?: any
}

const check = ({ rules, role, action, data }: Props) => {

	if(typeof role === "undefined") {
		return false;
	}

	const permissions = rules[role];

	if(!permissions) {
		return false;
	}

	const { static: staticPermissions, dynamic: dynamicPermissions } = permissions;

	if(staticPermissions && staticPermissions.includes(action)) {
		return true;
	}

	if(dynamicPermissions) {
		const permissionCondition = dynamicPermissions[action];


		if(typeof permissionCondition === "undefined") {
			return false;
		}

		return permissionCondition(data);
	}
	return false;
};

export { check };
