import React, {ReactNode} from "react";
import classNames from "classnames";
import "./styles.scss";

export type TBadgeType = "alt" | "primary" | "attention" | "darkgrey";
export type TBadgeSize = "normal" | "small";


type Props = {
	children?: ReactNode,
	type?: TBadgeType,
	size?: TBadgeSize,
	onClick?: () => void
}

const Badge = ({type, size, onClick, children}: Props) => {
	const classes = classNames("c-badge", `c-badge--${type}`, `c-badge--${size}`);

	return (
		<div className={classes} onClick={onClick} role={onClick ? "button" : "none"}>
			{children}
		</div>
	);
};

Badge.defaultProps = {
	type: "primary",
	size: "normal"
};

export default Badge;
