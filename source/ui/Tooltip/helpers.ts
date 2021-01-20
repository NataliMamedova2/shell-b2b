import {RefObject} from "react";
import {TTooltipSizeConfig} from "./Tooltip";

type TTooltipWrapperRect = {
	left: number,
	top: number,
	width: number,
	right: number
}

type TTooltipPosition = "left" | "right" | "center";

function getAnchorSide (anchorLeft: number, tooltipWidth: number): TTooltipPosition {
	const halfTooltip = tooltipWidth / 2;
	const { innerWidth: windowWidth } = window;
	const additionalSpace: number = 10;

	/**
	 *  [left|*|*]
	 */
	if(anchorLeft < (halfTooltip + additionalSpace)) {
		return "left";
	}
	/**
	 *  [*|*|right]
	 */
	if(anchorLeft > (windowWidth - (halfTooltip + additionalSpace))) {
		return "right";
	}

	/**
	 *  [*|center|*]
	 */
	return "center";
}

function calcTooltipPosition (anchorRect: TTooltipWrapperRect | null, tooltipType: TTooltipSizeConfig) {

	if(!anchorRect) {
		return {};
	}

	const { width: anchorWidth, left: anchorLeft, top: anchorTop } = anchorRect;
	const { width: tooltipWidth } = tooltipType;
	const screenSide: TTooltipPosition =  getAnchorSide(anchorLeft, tooltipWidth);
	const top = `${anchorTop}px`;

	if(screenSide === "left") {
		return {
			left: `${anchorLeft}px`,
			top
		};
	}

	if(screenSide === "right") {
		return {
			left: `${anchorLeft - (tooltipWidth - anchorWidth) }px`,
			top
		};
	}

	return {
		left: `${anchorLeft + (anchorWidth / 2) - (tooltipWidth / 2)}px`,
		top
	};
}


function getWrapperRect (ref: RefObject<HTMLSpanElement>): TTooltipWrapperRect | null {
	if(!ref.current) {
		return null;
	}

	const { left, top, width, right } = ref.current.getBoundingClientRect();
	return { left, top, width, right };
}

export { calcTooltipPosition, getWrapperRect };
