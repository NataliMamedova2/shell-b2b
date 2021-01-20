import {fieldsMapping, getField} from "../../config/fields";
import {TFieldType} from "@app-types/TFieldType";

const getSingleInput = (type: any) => {
	return typeof type === "string" ? getField({mapping: fieldsMapping, type: type as TFieldType}) : type;
};

const getButtonLabel = (buttonLabel: any, itemsCount: number) => {
	return typeof buttonLabel === "string" ? buttonLabel : itemsCount > 0 ? buttonLabel.plural : buttonLabel.single;
};

export {
	getSingleInput,
	getButtonLabel
};
