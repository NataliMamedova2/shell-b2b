import format from "date-fns/format";
import {TDateFormat} from "@app-types/TDateFormat";

type TFormatDateConfig = {
	date: number | Date,
	formatKey?: TDateFormat
}

const DATE_FORMATS: any = {
	"datetime": "dd.MM.yyyy HH:mm",
	"timedate": "HH:mm dd.MM.yyyy",
	"date": "dd.MM.yyyy",
	"time": "HH:mm"
};

const getPredefinedFormat = (format: string, formats: any = DATE_FORMATS): string => format in formats ? formats[format] : format;

const formatDate: ((config: TFormatDateConfig) => string) = ({ date, formatKey = "datetime" }) => {
	const formatString = getPredefinedFormat(formatKey);
	return format(new Date(date), formatString);
};

export {
	getPredefinedFormat,
	formatDate,
};
