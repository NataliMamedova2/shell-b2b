import React from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import classNames from "classnames";

type TInputCustom = {
	onFocus?: () => void
}

const Input = ({error, value, onChange, onFocus, options }: TSingleInput & TInputCustom ) => {
	const { inputType = "text", placeholder, disabled, maxLength } = options;

	const classes = classNames("c-input", {
		"is-error": error
	});
	return (
		<input
			className={classes}
			value={value}
			maxLength={maxLength}
			onFocus={onFocus}
			onChange={(e) => disabled ? () => {} : onChange(e.target.value)}
			disabled={disabled}
			type={inputType}
			placeholder={placeholder} />
	);
};

export default Input;
