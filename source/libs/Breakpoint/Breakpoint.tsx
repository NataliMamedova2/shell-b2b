import React, {ReactNode} from "react";
import { BreakpointContext } from "./BreakpointContext";
import { validateOnly, validateRange } from "./validate";

export type TRangeProp = [ string, string];

export type TOnlyProp = string[] | string;

type Props = {
	only?: TOnlyProp,
	range?: TRangeProp
	children?: ReactNode
}

const Breakpoint = ({ only, range, children }: Props) => {
	const validateMethod = only
		? validateOnly(only)
		: range
			? validateRange(range)
			: () => true;
	return (
		<BreakpointContext.Consumer>
			{
				(value: any) => {
					if(!validateMethod(value)) return null;
					return children;
				}
			}
		</BreakpointContext.Consumer>
	);

};

export { Breakpoint };
