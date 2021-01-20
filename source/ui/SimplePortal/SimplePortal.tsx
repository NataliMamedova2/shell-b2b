import React, {Component, ReactNode} from "react";
import "./styles.scss";
import {createPortal} from "react-dom";
import classNames from "classnames";

type Props = {
	className?: string,
	children: ReactNode
}

class SimplePortal extends Component<Props> {
	root: HTMLElement | null = document.querySelector("#overlay-root");

	render() {
		if(!this.root) {
			return null;
		}

		const classes = classNames("c-simple-portal", {
			[`${this.props.className}`]: this.props.className
		});

		return createPortal(
			<div className={classes}>{this.props.children }</div>,
			this.root
		);
	}
}

export default SimplePortal;
