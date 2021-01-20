import { TOnlyProp, TRangeProp} from "./Breakpoint";
import {breakpointConfig, TBreakpoint} from "./BreakpointProvider";

type TValidBreakpoint = (object: TBreakpoint) => boolean

const validateOnly = (props: TOnlyProp): TValidBreakpoint => {

	if(Array.isArray(props)) {
		return ({currentName}) => {
			return props.findIndex(i => i === currentName) !== -1;
		};
	}

	if(props && typeof props !== "object") {
		return ({currentName}) => currentName === props;
	}

	return () => true;
};

const validateRange = (props: TRangeProp): TValidBreakpoint => {

	const breakpoints: string[] = Object.keys(breakpointConfig);

	const [min, max] = [breakpoints.indexOf(props[0]), breakpoints.indexOf(props[1])];
	const [from, to] = [ Math.min(min, max), Math.max(min, max) ];

	const range = breakpoints.slice(from, to + 1);

	return validateOnly(range);
};

export { validateRange, validateOnly };
