import React from "react";
import MultiSelect, {multiSelectToOptions, TMultiSelectOption} from "../../../ui/MultiSelect/MultiSelect";
import {TSingleInput} from "@app-types/TSingleInput";
import {printShortDayOfWeek, printLongDayOfWeek} from "../../../config/dictionary";

type Props = Omit<TSingleInput, "value"> & {
	value: string[]
}

const WeekDaysSelector = ({value, options, onChange, error}: Props) => {

	const DAYS: TMultiSelectOption[] = [
		{ value: "mon", shortName: printShortDayOfWeek("mon"), longName: printLongDayOfWeek("mon") },
		{ value: "tue", shortName: printShortDayOfWeek("tue"), longName: printLongDayOfWeek("tue") },
		{ value: "wed", shortName: printShortDayOfWeek("wed"), longName: printLongDayOfWeek("wed") },
		{ value: "thu", shortName: printShortDayOfWeek("thu"), longName: printLongDayOfWeek("thu") },
		{ value: "fri", shortName: printShortDayOfWeek("fri"), longName: printLongDayOfWeek("fri") },
		{ value: "sat", shortName: printShortDayOfWeek("sat"), longName: printLongDayOfWeek("sat") },
		{ value: "sun", shortName: printShortDayOfWeek("sun"), longName: printLongDayOfWeek("sun") },
	];

	const daysValue: TMultiSelectOption[] = multiSelectToOptions(value, DAYS);

	return (
		<MultiSelect onChange={onChange} options={{optionsList:DAYS, ...options}} value={daysValue} error={error} />
	);
};

export default WeekDaysSelector;
