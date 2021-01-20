import React from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import classNames from "classnames";
import Icon from "../Icon";

const Select = ({ error, value, options, onChange }: TSingleInput) => {
	const {
		selectOptions,
		defaultLabel,
		defaultOption = "-1",
		disableDefaultOption = true
	} = options;

	if(!selectOptions || !defaultLabel) return null;

	const classes = classNames("c-select", {
		"is-error": error
	});

	return (
		<span className={classes}>
			<select
				className="c-select__native" value={value || defaultOption}
				onChange={(e) => onChange(e.target.value)}>
				<option value={ defaultOption } disabled={disableDefaultOption}>{defaultLabel}</option>
				{
					Object.entries(selectOptions).map(([key, value])=> <option key={key} value={key}>{value}</option>)
				}
			</select>

			<span className="c-select__arrow">
				<Icon type="triangle-down" />
			</span>
		</span>
	);
};

export default Select;
