import React, {ReactNode} from "react";
import classNames from "classnames";
import {Link} from "react-router-dom";

export type TTextType = "h1" | "h2" | "h3" | "h4" | "h5" | "paragraph" | "caption" | "link" | "note" | "label";
export type TTextColor = "light" | "dark" | "disable" | "darkgrey" | "error";

const defaultProps = {
	as: "p",
	type: "paragraph",
	color: "dark"
};

type Props = {
	as?: string,
	type?: TTextType,
	color?: TTextColor,
	className?: string
	children: ReactNode,
	to?: string,
	href?: string,
	underline?: boolean,
	bold?: boolean
}

const Text = ({ as = "p", className, type, color, children, to, underline, bold, ...props }: Props ) => {
	const typeModifier = `c-text--${type}`;
	const colorModifier = `a-color-${color}`;

	const classes = classNames("c-text", {
		[typeModifier]: type,
		[colorModifier]: color,
		"is-underlined": underline,
		"is-bold": bold,
		[className as string]: className
	});

	const options = {
		className: classes,
		...props
	};

	if(to) {
		return <Link to={to} {...options}>{children}</Link>;
	}

	return React.createElement(as, options, children);
};

Text.defaultProps = defaultProps;

export default Text;
