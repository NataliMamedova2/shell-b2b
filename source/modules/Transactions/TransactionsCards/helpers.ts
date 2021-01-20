import {TSimpleFormData} from "@app-types/TSimpleForm";
import {format} from "date-fns";
import {captureException} from "../../../vendors/exeptions";
import {TFiltersData} from "../../../stores/FiltersStore/FiltersStore";

const dataToFilters = (formData: TSimpleFormData): TFiltersData  => {
	const dataKeys: string[] = Object.keys(formData);

	return dataKeys.reduce((acc: TFiltersData, current: string) => {
		if(current === "dates") {
			const { startDate, endDate } = formData[current];

			if(startDate) {
				try {
					acc["dateFrom"] = format(startDate, "yyyy-MM-dd");
				} catch (e) {
					captureException(e);
				}
			}

			if(endDate) {
				try {
					acc["dateTo"] = format(endDate, "yyyy-MM-dd");
				} catch (e) {
					captureException(e);
				}
			}
			return acc;
		}
		if(current === "supply") {
			const { types, items } = formData[current];

			if(items && items.length > 0) {
				acc["supplies"] = items;
			}

			if(types && types.length > 0) {
				acc["supplyTypes"] = types;
			}

			return acc;
		}

		const currentValue = formData[current];
		const notEmptyArray = Array.isArray(currentValue) && currentValue.length > 0;
		const stringOrNumber = typeof currentValue === "string" || typeof currentValue === "number";

		if (currentValue && ( notEmptyArray || stringOrNumber )) {
			acc[current] = formData[current];
		}
		return acc;
	}, {});
};
const dataFromFilters = (filtersData: TFiltersData): TSimpleFormData => {

	const filtersKeys: string[] = Object.keys(filtersData);

	return filtersKeys.reduce((acc: TSimpleFormData, current: string) => {

		if(current === "dateFrom" || current === "dateTo") {
			if(!acc["dates"]) {
				acc["dates"] = {};
			}
			acc["dates"][current === "dateFrom" ? "startDate" : "endDate"] = new Date(filtersData[current] as string);
		}

		if(current === "supplies" || current === "supplyTypes") {
			if(!acc["supply"]) {
				acc["supply"] = {};
			}

			if(current === "supplies") {
				acc["supply"]["items"] = filtersData[current];
			}
			if(current === "supplyTypes") {
				acc["supply"]["types"] = filtersData[current];
			}
		}

		const currentValue = filtersData[current];
		const notEmptyArray = Array.isArray(currentValue) && currentValue.length > 0;
		const stringOrNumber = typeof currentValue === "string";

		if (currentValue && ( notEmptyArray || stringOrNumber )) {
			acc[current] = filtersData[current];
		}
		return acc;
	}, {});
};

export {
	dataFromFilters,
	dataToFilters
};
