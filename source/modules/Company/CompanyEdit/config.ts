import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";
import {PLACEHOLDER_EMAIL, PLACEHOLDER_PHONE} from "../../../config/constants";
import CustomLegalNameField from "./CustomLegalNameField";

const createCompanyProfileForm: TSimpleFormConfigFactory = (translate) => {
	return [
		{
			fields: [
				{
					type: "Input",
					label: translate("Company name in system"),
					key: "name",
					options: { },
					defaultValue: ""
				},
				{
					type: "Input",
					label: translate("Email of accounting"),
					key: "accountingEmail",
					options: {
						inputType: "email",
						placeholder: PLACEHOLDER_EMAIL
					},
					defaultValue: ""
				},
				{
					type: "Phone",
					label: translate("Phone of accounting"),
					key: "accountingPhone",
					options: {
						placeholder: PLACEHOLDER_PHONE
					},
					defaultValue: ""
				},
				{
					type: "Input",
					label: translate("Email chief"),
					key: "directorEmail",
					options: {
						disabled: true,
						inputType: "email",
						placeholder: PLACEHOLDER_EMAIL,
					},
					defaultValue: ""
				},
				{
					type: "Input",
					label: translate("Post address"),
					key: "postalAddress",
					options: { },
					defaultValue: ""
				},
				{
					type: CustomLegalNameField,
					label: translate("Legal company name"),
					key: "legalName",
					options: {},
					defaultValue: ""
				}
			],
			grid: [
				["name"],
				["accountingEmail", "accountingPhone"],
				["directorEmail", "postalAddress"],
				["legalName"]
			]
		}
	];
};

export default createCompanyProfileForm;
