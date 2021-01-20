import { TSimpleFormConfigFactory } from "@app-types/TSimpleForm";
import {PLACEHOLDER_EMAIL} from "../../../config/constants";

const createRestorePasswordForm: TSimpleFormConfigFactory = (translate) => {
	return [
		{
			fields: [
				{
					type: "Input",
					label: translate("Email"),
					key: "username",
					options: {
						placeholder: PLACEHOLDER_EMAIL,
						inputType: "email",
						showErrorPreview: true,
						errorPreview: translate("Invalid email"),
					},
					defaultValue: "",
				}
			],
			grid: [
				["username"]
			]
		}
	];
};

export default createRestorePasswordForm;
