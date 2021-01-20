import React, {Component, ReactNode} from "react";
import { BreakpointContext } from "./BreakpointContext";

type Props = {
	children?: ReactNode
}
type State = {
	currentName: string,
	currentWidth: number,
	state: TBreakpointState
}

type TBreakpointState = {
	isMobile?: boolean,
	isTablet?: boolean,
	isSmall?: boolean,
	isDesktop?: boolean,
	acrossMobileTablet?: boolean,
	acrossMobileSmall?: boolean,
	acrossTabletSmall?: boolean,
	upMobile?: boolean,
	upTablet?: boolean,
	upSmall?: boolean,
}

type TBreakpointConfig = { [key: string]: number }

export type TBreakpoint = State;

export const breakpointConfig: TBreakpointConfig = {
	mobile: 300,
	tablet: 768,
	small: 1025,
	desktop: 1301,
	large: 1600,
};


class BreakpointProvider extends Component<Props> {
	config: TBreakpointConfig = breakpointConfig;
	state: State = {
		currentName: "",
		currentWidth: 0,
		state: {}
	};

	componentDidMount(): void {
		this.update();
		window.addEventListener("resize", this.update);
	}

	componentWillUnmount(): void {
		window.removeEventListener("resize", this.update);
	}

	render() {

		const value: TBreakpoint = { ...this.state };

		return (
			<BreakpointContext.Provider value={value}>
				{this.props.children}
			</BreakpointContext.Provider>
		);
	}

	getCurrentName = (currentWidth: number): string => {

		return Object.keys(this.config).reduce((acc: string, current: string): string => {
			if(currentWidth >= this.config[current]) acc = current;
			return  acc;

		}, "");
	};

	getBreakpointStates = (currentWidth: number, currentName: string): TBreakpointState => {

		return {
			isMobile: currentName === "mobile",
			isTablet: currentName === "tablet",
			isSmall: currentName === "small",
			isDesktop: currentName === "desktop",
			acrossMobileTablet: currentName === "mobile" || currentName === "tablet",
			acrossMobileSmall: currentName === "mobile" || currentName === "tablet" || currentName === "small",
			acrossTabletSmall: currentName === "tablet" || currentName === "small",
			upMobile: currentWidth >= this.config["mobile"],
			upTablet: currentWidth >= this.config["tablet"],
			upSmall: currentWidth >= this.config["small"],
		};
	};

	update = () => {
		const { innerWidth: currentWidth }: Window = window;
		const currentName = this.getCurrentName(currentWidth);
		const state = this.getBreakpointStates(currentWidth, currentName);

		this.setState({
			currentWidth,
			currentName,
			state
		});
	}
}

export { BreakpointProvider };
