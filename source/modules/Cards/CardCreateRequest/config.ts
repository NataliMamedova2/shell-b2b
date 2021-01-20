import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";
import {PLACEHOLDER_PHONE} from "../../../config/constants";


const createRequestFormConfig: TSimpleFormConfigFactory = (translate) => {
	return [
		{
			fields: [
				{
					type: "InputNumber",
					label: translate("Number of cards"),
					key: "count",
					defaultValue: "",
					options: {
						placeholder: translate("Number of cards"),
						minValue: 1
					}
				}
			],
			grid: [ ["count", ""] ]
		},
		{
			title: translate("Delivery contact details"),
			fields: [
				{
					type: "Input",
					label: translate("Full name"),
					key: "name",
					defaultValue: "",
					options: { placeholder: translate("Your name") }
				},
				{
					type: "Phone",
					label: translate("Phone number"),
					key: "phone",
					defaultValue: "",
					options: { placeholder: PLACEHOLDER_PHONE }
				}
			],
			grid: [
				["name"],
				["phone", ""]
			]
		}
	];
};

export default createRequestFormConfig;
