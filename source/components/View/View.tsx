import React, {ReactNode } from "react";
import "./styles.scss";

type ViewProps = {
	children?: ReactNode,
	className?: string
}

const View = ({className, children}: ViewProps) => {

	const classes = `${className ? className : ""} m-view`;

	return (
		<div className={classes}>
			{children}
		</div>
	);
};

export default View;
