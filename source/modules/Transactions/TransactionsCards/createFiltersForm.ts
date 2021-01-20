import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";
import RangeDatepicker from "../../../components/RangeDatepicker";
import SuppliersSelectorField from "../../../components/SuppliersSelectorField";
import StationSelectorField from "./StationSelectorField";
import NumericInput from "../../../ui/Input/NumericInput";

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
			type: NumericInput,
			key: "cardNumber",
			label: translate("Card number"),
			defaultValue: "",
			options: {
				maxLength: 20
			},
		},
		{
			type: SuppliersSelectorField,
			key: "supply",
			label: "",
			defaultValue: {},
			options: {},
		},
		{
			type: StationSelectorField,
			key: "networkStations",
			label: translate("Network stations"),
			options: {},
			defaultValue: []
		},
		{
			type: "Select",
			key: "status",
			label: translate("Status"),
			defaultValue: "",
			options: {
				defaultLabel: translate("Select status of transaction"),
				defaultOption: "",
				disableDefaultOption: false,
				selectOptions: {
					"write-off": translate("Write off"),
					"return": translate("Return"),
					"replenishment": translate("Replenishment")
				}
			},

		},
	],
	grid: [
		["dates"],
		["networkStations"],
		["supply"],
		["cardNumber"],
		["status"],
	]
}]);

export {
	createFiltersForm
};
