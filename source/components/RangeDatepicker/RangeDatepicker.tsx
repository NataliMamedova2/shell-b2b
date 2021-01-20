import React from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import SingleField from "../../components/Field/SingleField";
import "./styles.scss";
import {useTranslation} from "react-i18next";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";

type RangeDatepickerProps = Omit<TSingleInput, "value"> & {
	value: {
		startDate: string,
		endDate: string
	}
}

const RangeDatepicker = ({ value, options, onChange }: RangeDatepickerProps) => {
	const { startDate, endDate } = value;
	const { fixedMinDate = null, fixedMaxDate = null, monthsOnly = false, disableMobile } = options;
	const { t } = useTranslation();

	const changeHandler = (key: string) => (newValue: string) => {
		onChange({ ...value, [key]: newValue});
	};

	const plugins = monthsOnly ? [
		// @ts-ignore
		new monthSelectPlugin({
			shorthand: true,
			dateFormat: "F Y",
			theme: "red"
		})
	] : [];

	return (
		<div className="c-range-datepicker">
			<SingleField
				value={startDate}
				type="Date"
				key={"_start_".concat(endDate)}
				onChange={changeHandler("startDate")}
				options={{
					placeholder: t("Select start of period"),
					minDate: fixedMinDate,
					maxDate: endDate || fixedMaxDate || null,
					plugins: plugins,
					disableMobile
				}}
				label={ t("Start of period") } />
			<SingleField
				value={endDate}
				type="Date"
				onChange={changeHandler("endDate")}
				key={"_end_".concat(startDate)}
				options={{
					placeholder: t("Select end of period"),
					minDate: startDate || fixedMinDate || null,
					maxDate: fixedMaxDate,
					plugins: plugins,
					disableMobile
				}}
				label={ t("End of period") } />
		</div>
	);
};

export default React.memo(RangeDatepicker);
