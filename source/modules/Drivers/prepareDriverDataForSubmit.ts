import {TSimpleFormData} from "@app-types/TSimpleForm";

type Dict<T = any> = { [key: string]: T }

const preparingDriverDataForSubmit = (data: TSimpleFormData) => {
	return Object.entries(data).reduce((acc: Dict, [key, value]: any) => {
		const keys = ["carsNumbers", "phones"];

		if(keys.includes(key) && Array.isArray(value)) {
			acc[key] = value.map(item => typeof item === "string" ? { number: item } : item);
		} else {
			acc[key] = value;
		}

		return acc;
	}, {});
};

export { preparingDriverDataForSubmit };
