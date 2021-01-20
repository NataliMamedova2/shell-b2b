import {
	TSimpleFormConfigFactory,
	TSimpleFormDynamicValidator,
	TSimpleFormField,
	TSimpleFormGrid
} from "@app-types/TSimpleForm";
import {PLACEHOLDER_EMAIL, PLACEHOLDER_PHONE} from "../../config/constants";
import {TFunction} from "i18next";
import {translate} from "../../libs";

const userFieldsFactory = (translate: TFunction): TSimpleFormField[] => ([{
	type: "Input",
	label: translate("First name"),
	key: "firstName",
	options: {
		placeholder: translate("First name")
	},
	defaultValue: "",
},
	{
		type: "Input",
		label: translate("Last name"),
		key: "lastName",
		options: {
			placeholder: translate("Last name")
		},
		defaultValue: ""
	},
	{
		type: "Input",
		label: translate("Surname"),
		key: "middleName",
		options: {
			placeholder: translate("Surname")
		},
		defaultValue: ""
	},
	{
		type: "Select",
		label: translate("Access role"),
		key: "role",
		options: {
			placeholder: "",
			defaultLabel: translate("Select the access level"),
			selectOptions: {
				"admin": translate("Admin"),
				"accountant": translate("Accountant"),
				"manager": translate("Manager"),
			}
		},
		defaultValue: ""
	},
	{
		type: "Input",
		label: translate("Email"),
		key: "email",
		options: {
			placeholder: PLACEHOLDER_EMAIL,
			inputType: "email"
		},
		defaultValue: ""
	},
	{
		type: "Phone",
		label: translate("Phone"),
		key: "phone",
		options: {
			placeholder: PLACEHOLDER_PHONE
		},
		defaultValue: ""
	},
	{
		type: "Input",
		label: translate("Username"),
		key: "username",
		options: {
			placeholder: translate("Example, auchan-accountan"),
		},
		defaultValue: ""
	},
	{
		type: "Password",
		label: translate("Password"),
		key: "password",
		options: {
			placeholder: "",
			errorPreview: translate("Passwords must match"),
		},
		defaultValue: ""
	},
	{
		type: "Password",
		label: translate("Re-enter password"),
		key: "rePassword",
		options: {
			placeholder: "",
			errorPreview: translate("Passwords must match"),
		},
		defaultValue: ""
	}
]);

export const CREATE_USER_FORM_GRID: TSimpleFormGrid[] = [
	["firstName", "lastName"],
	["middleName", "role"],
	["email", "phone"],
	["username", ""],
	["password", "rePassword"],
];

export const EDIT_PROFILE_FORM_GRID: TSimpleFormGrid[] = [
	["firstName", "lastName"],
	["middleName", ""],
	["email", "phone"],
	["username", ""],
	["password", "rePassword"],
];

export const createUserFormConfig = (grid: TSimpleFormGrid[]): TSimpleFormConfigFactory => (translate) => {
	return [
		{
			fields: userFieldsFactory(translate),
			grid
		}
	];
};


export const matchPasswords: TSimpleFormDynamicValidator = (data) => {
	const { password, rePassword } = data;

	if( (password.length > 0) && password !== rePassword ) {
		return {
			password: [
				translate("The both password must be the same"),
				translate("Use password preview for make sure you haven't any typo errors")
			],
			rePassword: [
				translate("The both password must be the same")
			]
		};
	}
	return {};
};
