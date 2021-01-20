import React from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import classNames from "classnames";

const PhoneInput = ({error, value, onChange, options }: TSingleInput) => {
	const { placeholder, disabled } = options;
	const classes = classNames("c-input", {
		"is-error": error
	});

	return (
		<input
			maxLength={15}
			className={classes}
			value={value}
			onChange={(e) => onChange(e.target.value)}
			disabled={disabled}
			type="tel"
			placeholder={placeholder} />
	);
};

export default PhoneInput;
