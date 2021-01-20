import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";
import RangeDatepicker from "../../../components/RangeDatepicker";

const createFiltersForm: TSimpleFormConfigFactory =  (translate) => ([{
	fields: [
		{
			type: RangeDatepicker,
			key: "dates",
			label: "",
			defaultValue: {},
			options: {
				labelAs: "span"
			},
		},
		{
			type: "Select",
			key: "type",
			label: translate("Type of transaction"),
			defaultValue: "",
			options: {
				defaultLabel: translate("Type of transaction"),
				defaultOption: "",
				disableDefaultOption: false,
				selectOptions: {
					"write-off-cards": translate("Write off"),
					"refill": translate("Refill"),
					"discount": translate("Discount")
				}
			},

		},
	],
	grid: [
		["dates"],
		["type"],
	]
}]);

export {
	createFiltersForm
};
