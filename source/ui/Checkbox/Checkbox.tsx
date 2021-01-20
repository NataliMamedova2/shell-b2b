import React, {} from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import {Paragraph} from "../Typography";
import classNames from "classnames";

const Checkbox = ({ error, value, options, onChange }: TSingleInput) => {
	const boolValue = Boolean(value);
	const { label } = options;

	const classes = classNames("c-checkbox", {
		"is-error": error
	});

	return (
		<label className={classes} role="button">
			<input
				className="c-checkbox__native"
				type="checkbox"
				checked={boolValue}
				onChange={(e) => onChange(e.target.checked)}
			/>
			<span className="c-checkbox__icon" />
			<Paragraph className="c-checkbox__label">{label}</Paragraph>
		</label>
	);
};

export default Checkbox;
