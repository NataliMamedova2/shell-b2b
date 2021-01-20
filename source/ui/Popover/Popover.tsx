import React, {ReactNode, SyntheticEvent, useState} from "react";
import classNames from "classnames";
import "./styles.scss";
import WindowEvents from "../WindowEvents";

const defaultProps = {
	to: "top-center"
};

type Props = {
	children: ReactNode | null,
	content: ReactNode,
	anchor: "top-left" | "top-right" | "top-center" | "bottom-left" | "bottom-right" | "bottom-center",
	className?: string
}

const Popover = ({children, content, className, anchor }: Props) => {
	const [active, setActive] = useState(false);

	const classes = classNames("c-popover", {
		[className as string]: className
	});

	const contentClasses = classNames("c-popover__content", `is-to-${anchor}`);

	const show = () => setActive(true);
	const hide = () => setActive(false);
	const prevent = (e: SyntheticEvent) => {
		e.preventDefault();
		e.stopPropagation();
	};

	return (
		<div role="button" className={classes} onMouseOver={show} onMouseLeave={hide} onClick={prevent}>
			{children}
			{
				active && (
					<div className={contentClasses}>
						<WindowEvents onClick={hide} onScroll={hide} onResize={hide}/>
						{ content }
					</div>
				)
			}
		</div>
	);
};

Popover.defaultProps = defaultProps;

export default Popover;
