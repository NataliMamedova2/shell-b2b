import {createContext, useContext} from "react";
import {TBreakpoint} from "./BreakpointProvider";

const BreakpointContext = createContext<TBreakpoint>({ state: {}, currentName: "", currentWidth: window.innerWidth || 1280 });

const useBreakpoint = () => {
	const state = useContext(BreakpointContext);

	if (!state) {
		throw new Error("No provider for Breakpoint Context!");
	}

	return state;
};

export { useBreakpoint, BreakpointContext };
