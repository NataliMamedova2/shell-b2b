import React from "react";
import "./styles.scss";
import {H4, Label} from "../../ui/Typography";
import classNames from "classnames";

type Props = {
	label: string,
	value: string | number,
	className?: string
}

const Score = ({label, value, className}: Props) => {

	const classes = classNames("c-score", {
		[className as string]: className
	});

	return (
		<div className={classes}>
			<Label>{ label }</Label>
			<H4>{ value }</H4>
		</div>
	);
};

export default Score;
