import React from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import SingleField from "../../components/Field/SingleField";

type TDriverSingleInput = {
	value: {
		number: string
	}
} & Omit<TSingleInput, "value">

const updateHandler = (updateMethod:  ( data: { number: string }) => void ) => (val: string) => ( updateMethod({ number: val }) );
const maybeErrors = (errors: any, key: string) =>  errors ? errors[key]: null;
const maybeValue = (value: any, key: string) => value && value[key] ? value[key].toString() : "";

const DriverPhoneField = ({value, options, onChange, errors}: TDriverSingleInput) => {

	return <SingleField
		value={maybeValue(value, "number")}
		onChange={updateHandler(onChange)}
		options={options}
		errors={maybeErrors(errors, "number")}
		label=""
		type="Phone" />;
};

const DriverCarField = ({value, options, onChange, errors}: TDriverSingleInput) => {

	return <SingleField
		value={maybeValue(value, "number")}
		onChange={updateHandler(onChange)}
		options={options}
		errors={maybeErrors(errors,"number")}
		label=""
		type="Input" />;
};


export {
	DriverPhoneField,
	DriverCarField
};
