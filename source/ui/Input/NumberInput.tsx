import React from "react";
import Input from "./Input";
import {TSingleInput} from "@app-types/TSingleInput";

type TNumberInput = Omit<TSingleInput, "value"> & { value: number }

const onlyDigits = (val: string) => {
	const cleanVal = val.toString().replace(/\D/g, "").replace(/^0/, "");

	if(!val || !cleanVal) {
		return "0";
	}
	return cleanVal;
};

const defaultOptions = {
	maxLength: 6,
	minValue: 0
};

const NumberInput = ({ value, onChange, error, errors, options}: TNumberInput) => {
	const preferences = { ...defaultOptions, ...options};
	const inputValue = value ? value.toString() : "0";

	const changeHandler = (val: string) => {
		onChange(onlyDigits(val));
	};

	return (
		<Input value={inputValue} onChange={changeHandler} options={preferences} errors={errors} error={error} />
	);
};

export default NumberInput;
