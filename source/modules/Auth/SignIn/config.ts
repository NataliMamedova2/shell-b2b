import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";

const createSignInForm: TSimpleFormConfigFactory = (translate) => ([
	{
		fields: [
			{
				type: "Input",
				label: translate("Username"),
				key: "username",
				options: {
					errorPreview: translate("Username invalid")
				},
				defaultValue: "",
			},
			{
				type: "Password",
				label: translate("Password"),
				key: "password",
				options: {
					errorPreview: translate("Password invalid")
				},
				defaultValue: "",
			}
		],
		grid: [
			["username"],
			["password"],
		]
	}
]);

export default createSignInForm;
