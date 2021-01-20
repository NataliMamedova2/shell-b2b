import React from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import classNames from "classnames";
import { Paragraph } from "../Typography";

const RadioGroup = ({value, onChange, options}: TSingleInput) => {
	const { radioOptions = {}, orientation = "vertical" } = options;

	const classes = classNames("c-radio-group", `is-${orientation}`);

	return (
		<div className={classes}>
			{
				Object.keys(radioOptions).map(radioKey => {
					return (
						<label className="c-radio-group__item" role="button" key={radioKey}>
							<input
								className="c-radio-group__native"
								type="radio"
								value={radioKey}
								checked={radioKey === value}
								onChange={(e) => onChange(e.target.value)}
							/>
							<span className="c-radio-group__icon" />
							<Paragraph className="c-radio-group__label">{ radioOptions[radioKey] }</Paragraph>
						</label>
					);
				})
			}
		</div>
	);
};
export default RadioGroup;
