import {TTooltipSize, TTooltipSizeConfig} from "./Tooltip";

const tooltipTypes: { [K in TTooltipSize]: TTooltipSizeConfig } = {
	"small": {
		width: 100
	},
	"medium": {
		width: 160
	},
	"large": {
		width: 220
	},
	"extra": {
		width: 280
	}
};

export { tooltipTypes };
