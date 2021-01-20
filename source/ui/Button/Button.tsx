import React, {ReactNode} from "react";
import { TButtonType } from "@app-types/TButtonType";
import { TIconType } from "@app-types/TIconType";
import classNames from "classnames";
import Icon from "../Icon";
import {Link} from "react-router-dom";
import "./styles.scss";

const defaultProps = {
	type: "primary",
	direction: "normal",
	pending: false,
	disabled: false
};

type Props = {
	children?: ReactNode,
	icon?: TIconType,
	type?: TButtonType,
	direction?: "normal" | "reverse",
	pending?: boolean
	disabled?: boolean,
	to?: string,
	asButton?: boolean,
	href?: string,
	className?: string,
	onClick?: (v?: any) => void
};

const Button = ({asButton = false, icon, type, pending, href, direction, disabled, to, className, onClick, children}: Props) => {

	const isNormalDirection = direction === "normal";

	const classes = classNames("c-button", `c-button--${type}`, {
		"is-pending": pending,
		"is-disabled": disabled,
		"has-icon-left": icon && isNormalDirection,
		"has-icon-right": icon && !isNormalDirection,
		[className as any]: className
	});

	if(to && !onClick) {
		return (
			<Link to={to} className={classes}>
				{ icon && isNormalDirection && <Icon type={icon} pending={pending} />}
				<span className="c-button__label">{ children }</span>
				{ icon && !isNormalDirection && <Icon type={icon} pending={pending} /> }
			</Link>
		);
	}

	if(href) {
		return (
			<a href={href} className={classes} rel="noopener noreferrer" target="_blank">
				{ icon && isNormalDirection && <Icon type={icon} pending={pending} /> }
				<span className="c-button__label">{ children }</span>
				{ icon && !isNormalDirection && <Icon type={icon} pending={pending} /> }
			</a>
		);
	}

	if(asButton) {
		return (
			<button type="button" className={classes} onClick={onClick}>
				{ ((icon && isNormalDirection) || (isNormalDirection && pending)) && <Icon type={icon} pending={pending} />}
				<span className="c-button__label">{ children }</span>
				{ ((icon && !isNormalDirection) || (!isNormalDirection && pending)) && <Icon type={icon} pending={pending} /> }
			</button>
		);
	}

	return (
		<div role="button" className={classes} onClick={onClick}>
			{ ((icon && isNormalDirection) || (isNormalDirection && pending)) && <Icon type={icon} pending={pending} />}
			<span className="c-button__label">{ children }</span>
			{ ((icon && !isNormalDirection) || (!isNormalDirection && pending)) && <Icon type={icon} pending={pending} /> }
		</div>
	);
};

Button.defaultProps = defaultProps;

export default Button;
