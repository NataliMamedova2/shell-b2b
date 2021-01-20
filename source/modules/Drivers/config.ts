import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";
import {PLACEHOLDER_EMAIL, PLACEHOLDER_PHONE} from "../../config/constants";
import {DriverPhoneField, DriverCarField} from "./DriverCustomFields";



const createDriverEditForm: TSimpleFormConfigFactory = (translate) => ([
	{
		fields: [
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
				label: translate("First name"),
				key: "firstName",
				options: {
					placeholder: translate("First name")
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
				type: "Input",
				label: translate("Email"),
				key: "email",
				options: {
					placeholder: PLACEHOLDER_EMAIL,
					inputType: translate("Email")
				},
				defaultValue: ""
			},
			{
				type: "Select",
				key: "status",
				label: translate("Status"),
				options: {
					defaultLabel: translate("Select driver status"),
					selectOptions: {
						"active": translate("Active driver"),
						"blocked": translate("Blocked driver")
					}
				},
				defaultValue: "active"
			}
		],
		grid: [
			["lastName", "firstName"],
			["middleName", "email"],
			["status"]
		]
	},
	{
		fields: [{
			type: "Array",
			key: "phones",
			label: translate("Phone"),
			options: {
				arrayOptions: {
					type: DriverPhoneField,
					defaultValue: "",
					buttonLabel: translate("Add phone number"),
					removeFrom: 1,
				},
				placeholder: PLACEHOLDER_PHONE
			},
			defaultValue: [{ number: "" }]
		}],
		grid: [["phones", ""]]
	},
	{
		fields: [{
			type: "Array",
			key: "carsNumbers",
			label: translate("Car number"),
			options: {
				arrayOptions: {
					type: DriverCarField,
					defaultValue: "",
					buttonLabel: translate("Add car number"),
					removeFrom: 1,
					maxLength: 15
				}
			},
			defaultValue: []
		}],
		grid: [["carsNumbers", ""]]
	},
	{
		fields: [{
			type: "Textarea",
			key: "note",
			label: translate("Note"),
			options: {
				placeholder: translate("Add some notes about this driver. They will be appear in drivers list or other places in system"),
				maxLength: 500
			},
			defaultValue: ""
		}],
		grid: [["note"]]
	}
]);

export default createDriverEditForm;
