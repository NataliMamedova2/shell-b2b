import { TSimpleFormConfigFactory } from "@app-types/TSimpleForm";
import {PLACEHOLDER_EMAIL} from "../../config/constants";

const createFeedbackForm: TSimpleFormConfigFactory = (translate) =>([
	{
		fields: [
			{
				key: "category",
				label: translate("Application category"),
				type: "Select",
				options: {
					defaultLabel: translate("Application category"),
					selectOptions: {
						"general-question": translate("general-question"),
						"financial-issue": translate("financial-issue"),
						"new-card-order": translate("new-card-order"),
						"complaints": translate("complaints")
					}
				},
				defaultValue: ""
			},
			{
				key: "email",
				label: translate("Contact Email"),
				type: "Input",
				options: {
					inputType: "email",
					placeholder: PLACEHOLDER_EMAIL
				},
				defaultValue: ""
			},
			{
				key: "name",
				label: translate("Ful name"),
				type: "Input",
				options: {
					placeholder: translate("Ful name")
				},
				defaultValue: ""
			},
			{
				key: "comment",
				label: translate("Comment"),
				type: "Textarea",
				options: {
					placeholder: translate("Write down your message here")
				},
				defaultValue: ""
			},

		],
		grid: [
			["category"],
			["email", "name"],
			["comment"]
		]
	},
]);

export default createFeedbackForm;
