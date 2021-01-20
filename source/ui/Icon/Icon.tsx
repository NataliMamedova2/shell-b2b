import React  from "react";
import classNames from "classnames";
import { TIconType } from "@app-types/TIconType";
import "./styles.scss";

type Props = {
	type?: TIconType,
	pending?: boolean,
	tabIndex?: number,
	onClick?: () => void
}

const Icon = ({tabIndex = -1, type = "bug", pending= false, onClick }: Props) => {

	const iconType = pending ? "c-icon--pending" : `c-icon--${type}`;

	const classes = classNames("c-icon", iconType, {
		"is-pending": pending,
		"is-actionable": typeof onClick !== "undefined"
	});

	return (
		<span className={classes} onClick={onClick} role={onClick ? "button" : "icon"} tabIndex={tabIndex} />
	);
};

export default Icon;
