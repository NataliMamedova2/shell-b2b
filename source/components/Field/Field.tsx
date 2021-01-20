import React from "react";
import "./styles.scss";
import SingleField from "./SingleField";
import ArrayField from "./ArrayField";
import {TSimpleFormField} from "@app-types/TSimpleForm";
import {TSingleInput} from "@app-types/TSingleInput";
import {useTranslation} from "react-i18next";

type TFieldProps = {
	field: TSimpleFormField,
	errors: any,
	value: any,
} & Omit<TSingleInput, "value" | "options">



const Field = ({field,error, errors, onChange, value, ...props}: TFieldProps) => {
	const { type, options, label } = field;
	const { t } = useTranslation();
	const translatedLabel  = label ? t(label) : label;


	if(type === "Array") {
		return (
			<ArrayField
				errors={errors}
				value={value}
				onChange={onChange}
				options={options}
				label={translatedLabel}
			/>
		);
	}

	return (
		<SingleField
			errors={ errors }
			value={value}
			onChange={onChange}
			options={options}
			label={translatedLabel}
			type={type}/>
	);

};

export default Field;
