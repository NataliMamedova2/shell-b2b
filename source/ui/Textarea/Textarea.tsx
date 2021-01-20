import React from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import TextareaAutosize from "react-textarea-autosize";
import classNames from "classnames";

const Textarea = ({error, value, onChange, options }: TSingleInput) => {
	const { placeholder, disabled } = options;

	const classes = classNames("c-textarea", {
		"is-error": error
	});

	return (
		<TextareaAutosize
			minRows={5}
			maxRows={10}
			className={classes}
		  value={value}
		  onChange={(e) => onChange(e.target.value)}
		  disabled={disabled}
		  placeholder={placeholder} />
	);
};

export default Textarea;
