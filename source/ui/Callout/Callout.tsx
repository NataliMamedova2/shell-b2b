import React, {ReactNode} from "react";
import "./styles.scss";
import Icon from "../Icon";
import {Paragraph} from "../Typography";
import classNames from "classnames";
import {TIconType} from "@app-types/TIconType";

type Props = {
	children?: ReactNode,
	icon?: TIconType,
	type?: "default" | "simple" | "inform"
}

const Callout = (
	{
		type = "default",
		icon = "info",
		children
	}: Props) => {

	const classes = classNames("c-callout", `c-callout--${type}`);

	return (
		<div className={classes}>
			<Icon type={icon} />
			<Paragraph className="c-callout__text">
				{children}
			</Paragraph>
		</div>
	);
};

export default Callout;
