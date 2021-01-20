import {getScrollbarWidth, getViewport, TScrollbarSize, TViewportInfo, updateRootRule} from "./helpers";

class SystemStore {
	scrollbarWidth: TScrollbarSize = getScrollbarWidth();
	viewport: TViewportInfo = getViewport();

	constructor() {
		updateRootRule("--windowHeight", this.viewport.height + "px");
		updateRootRule("--scrollbarWidth", this.scrollbarWidth + "px");


		window.addEventListener("resize", () => {
			const viewport = this.setViewport();
			const scrollbarWidth = getScrollbarWidth();

			updateRootRule("--windowHeight", viewport.height + "px");
			updateRootRule("--scrollbarWidth", scrollbarWidth + "px");
		});
	}

	setViewport = () => {
		this.viewport = getViewport();
		return this.viewport;
	};
	setScrollbar = () => {
		this.scrollbarWidth = getScrollbarWidth();
		return this.scrollbarWidth;
	};
}

export default SystemStore;
