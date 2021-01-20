import React, { ReactNode } from "react";
import "./styles.scss";
import classNames from "classnames";


type Props = {
	as?: any,
	children?: ReactNode,
	square?: boolean,
	className?: string
}

const Paper = ({ as, square, children, className, ...props}: Props) => {
	const classes = classNames("c-paper", {
		"is-square": square,
		[className as any]: className
	});

	const options = {
		className: classes,
		...props
	};

	return React.createElement(
			as,
			options,
			children
		);
};

Paper.defaultProps = {
	as: "div"
};

export default Paper;
