import React, {CSSProperties, ReactNode, SyntheticEvent, useRef} from "react";
import { observer } from "mobx-react";
import classNames from "classnames";
import "./styles.scss";
import Icon from "../Icon";
import { Note } from "../Typography";
import WindowEvents from "../WindowEvents";
import {useBreakpoint} from "../../libs/Breakpoint";
import SimplePortal from "../SimplePortal";
import appUIStore from "../../stores/AppUIStore";
import {calcTooltipPosition, getWrapperRect} from "./helpers";
import {tooltipTypes} from "./config";

export type TTooltipSize = "small" | "medium" | "large" | "extra";

export type TTooltipSizeConfig = {
	width: number
}

type Props = {
	children?: ReactNode,
	message: ReactNode,
	className?: string,
	danger?: boolean,
	ellipsis?: boolean,
	size?: TTooltipSize,
	tooltipKey: string
}

type ContentProps = {
	calcPositionStyles: () => CSSProperties,
	onClose: () => void
} & Pick<Props, "message">

const TooltipContent = React.memo(({message, calcPositionStyles, onClose}: ContentProps) => (
	<SimplePortal>
		<WindowEvents onClick={onClose} onScroll={onClose} onResize={onClose}/>
		<div className="c-tooltip__content" style={{...calcPositionStyles()}}>
			{
				typeof message === "string" ? <Note>{ message }</Note> : message
			}
		</div>
	</SimplePortal>
));

const Tooltip = ({className, message, size = "small", danger, tooltipKey, ellipsis, children}: Props) => {
	const { currentOverLayer, setOverLayer } = appUIStore;
	const { state: { acrossMobileTablet } } = useBreakpoint();
	const tooltipRef: any = useRef(null);

	const prevent = (e: SyntheticEvent) => {
		e.stopPropagation();
		e.preventDefault();
	};

	const overHandler = () => {
		if(tooltipRef === null) { return false; }
		setOverLayer(tooltipKey);
	};

	const clickHandler = (e: SyntheticEvent) => {
		prevent(e);
		overHandler();
	};
	const leaveHandler = () => setOverLayer(null);

	const targetEvents = acrossMobileTablet
		? { onClick: clickHandler }
		: { onMouseEnter: overHandler, onMouseLeave: leaveHandler, onClick: prevent };

	const calcPositionStyles = () => {
		return {
			width: `${tooltipTypes[size].width}px`,
			...calcTooltipPosition(getWrapperRect(tooltipRef), tooltipTypes[size])
		};
	};

	return (
		<span
			className={classNames("c-tooltip", {
				[className as string]: className,
				"is-danger": danger,
				"is-ellipsis": ellipsis
			})}
			ref={tooltipRef}
			{...targetEvents}
		>
			{ children ? children : <Icon type="info" /> }
			{
				(currentOverLayer === tooltipKey) && (
					<TooltipContent message={message} calcPositionStyles={calcPositionStyles} onClose={leaveHandler}/>
				)
			}
		</span>
	);
};

export default observer(Tooltip);
