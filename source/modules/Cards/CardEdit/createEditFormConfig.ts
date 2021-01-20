import {TSimpleFormConfigFactory} from "@app-types/TSimpleForm";
import CustomLimitsField from "./CustomLimitsField";
import CustomTotalLimitsField from "./CustomTotalLimitsField";
import WeekDaysSelector from "./WeekDaysSelector";
import {customLimits} from "../limitsStore";

const DEFAULT_TOTAL_LIMITS = {
	day: 0,
	week: 0,
	month: 0,
};

const DEFAULT_CUSTOM_LIMITS = {
	name: "",
	dayLimit: 0,
	weekLimit: 0,
	monthLimit: 0,
};

const { fuelLimits, goodsLimits, servicesLimits } = customLimits.mapKeys();


const createEditFormConfig: TSimpleFormConfigFactory = (translate) => {
	return [
		{
			fields: [{
				key: "totalLimits",
				label: "",
				type: CustomTotalLimitsField,
				options: {
					showErrorPreview: false
				},
				defaultValue: { ...DEFAULT_TOTAL_LIMITS }
			}],
			grid: [[ "totalLimits" ]]
		},
		{
			title: translate("Limits for fuels"),
			titleInfo: translate("Day, week and month must be consistent"),
			fields: [{
				key: fuelLimits,
				type: "Array",
				label: "",
				options: {
					arrayOptions: {
						type: CustomLimitsField,
						buttonLabel: translate("Add limit"),
						disableRemoveButtons: (items: any) => items.length < 2,
						defaultValue: {...DEFAULT_CUSTOM_LIMITS},
						removeIcon: "close",
					},
					labelsPostfix: translate("l"),
					searchTitle: translate("Fuel"),
					searchExtractKey: "name",
					searchEndpoint: "/fuel/search",
					storeKey: fuelLimits,
				},
				defaultValue: []
			}],
			grid: [[fuelLimits]]
		},
		{
			title: translate("Limits by usage time"),
			fields: [
				{
					key: "startUseTime",
					type: "Date",
					defaultValue: "",
					options: {mode: "time", placeholder: translate("Select start time")}, label: translate("Service start time")
				},
				{
					key: "endUseTime",
					type: "Date",
					defaultValue: "",
					options: {mode: "time", placeholder: translate("Select end time")}, label: translate("Service end time")
				},
				{
					key: "serviceDays",
					type: WeekDaysSelector,
					defaultValue: [],
					options: {
						allSelectedLabel: translate("Everyday"),
						noSelectedLabel: translate("No selected days"),
						placeholder: translate("Service days"),
						selectAllButton: true
					},
					label: translate("Service days")
				}
			],
			grid: [
				["startUseTime", "endUseTime"],
				["serviceDays", ""]
			]
		},
		{
			title: translate("Goods"),
			fields: [{
				key: goodsLimits,
				type: "Array",
				label: "",
				options: {
					arrayOptions: {
						type: CustomLimitsField,
						buttonLabel: translate("Add product"),
						defaultValue: {...DEFAULT_CUSTOM_LIMITS},
						removeIcon: "close",
					},
					labelsPostfix: translate("pieces"),
					searchTitle: translate("Goods"),
					searchExtractKey: "name",
					searchEndpoint: "/goods/search",
					storeKey: goodsLimits,
				},
				defaultValue: []
			}],
			grid: [[goodsLimits]]
		},
		{
			title: translate("Services"),
			fields: [{
				key: servicesLimits,
				type: "Array",
				label: "",
				options: {
					arrayOptions: {
						type: CustomLimitsField,
						buttonLabel: translate("Add service"),
						defaultValue: {...DEFAULT_CUSTOM_LIMITS},
						removeIcon: "close",
					},
					labelsPostfix: translate("uah"),
					searchTitle: translate("Services"),
					searchExtractKey: "name",
					searchEndpoint: "/services/search",
					storeKey: servicesLimits,
				},
				defaultValue: []
			}],
			grid: [[servicesLimits]]
		},
	];
};




export default createEditFormConfig;
