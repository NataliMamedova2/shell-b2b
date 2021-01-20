import React from "react";
import Input from "./Input";
import {TSingleInput} from "@app-types/TSingleInput";

const extractDigits = (val: string) => {
	return val.replace(/\D/g, "");
};
const defaultOptions = {
	maxLength: 10,
	minValue: 0
};

const NumericInput = ({ value, onChange, error, errors, options}: TSingleInput) => {
	const preferences = { ...defaultOptions, ...options};
	const inputValue = extractDigits(value);

	const changeHandler = (val: string) => onChange(extractDigits(val));

	return (
		<Input value={inputValue} onChange={changeHandler} options={preferences} errors={errors} error={error} />
	);
};

export default NumericInput;
