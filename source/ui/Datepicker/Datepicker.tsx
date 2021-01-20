import React from "react";
import "./styles.scss";
import ReactFlatpickr from "../../libs/ReactFlatpickr/index";
import {TSingleInput} from "@app-types/TSingleInput";
import classNames from "classnames";
import Icon from "../Icon";
import format from "date-fns/format";
import { Ukrainian } from "flatpickr/dist/l10n/uk";
import { english } from "flatpickr/dist/l10n/default";
import { getCurrentLanguage } from "../../config/i18n/getCurrentLanguage";

const TIME_OPTIONS = { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true };

const Datepicker = ({ error, value, onChange, options }: TSingleInput) => {

	const { placeholder, mode, minDate = null, maxDate = null, plugins } = options;
	const disableMobile = typeof options.disableMobile !== "undefined" ? options.disableMobile : false;
	const isTimeMode = mode === "time";
	const locale = getCurrentLanguage() === "uk" ? Ukrainian : english;

	const classes = classNames("c-datepicker", {
		"is-timepicker": isTimeMode
	});

	const inputClasses = classNames("c-datepicker__input", {
		"is-error": error,
	});

	const justTimeOptions = isTimeMode ? TIME_OPTIONS : { minDate, maxDate, plugins, disableMobile };
	const changeHandler = (dateRange: any) => {
		const date = dateRange[0];
		const formattedValue = isTimeMode ? format(date, "HH:mm") : date;

		return onChange(formattedValue);
	};

	return (
		<div className={classes}>
			<Icon type="calendar" />
			<ReactFlatpickr
				options={{ ...justTimeOptions, locale }}
				className={inputClasses}
				placeholder={placeholder}
				value={value}
				onChange={changeHandler} />
		</div>

	);
};

export default React.memo(Datepicker);




