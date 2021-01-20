export type TViewportInfo = {
	width: number,
	height: number,
	orientation: "portrait" | "landscape"
}

export type TScrollbarSize = number;

function getScrollbarWidth(): TScrollbarSize {
	const outer: HTMLDivElement = document.createElement("div");
	outer.style.visibility = "hidden";
	outer.style.width = "100px";

	document.body.appendChild(outer);

	const widthNoScroll = outer.offsetWidth;
	outer.style.overflow = "scroll";
	const inner = document.createElement("div");
	inner.style.width = "100%";
	outer.appendChild(inner);

	const widthWithScroll = inner.offsetWidth;
	
	if(outer.parentNode) {
		outer.parentNode.removeChild(outer);	
	}
	
	return widthNoScroll - widthWithScroll;
}

function getViewport(): TViewportInfo {
	const { innerWidth: width, innerHeight: height } = window;
	const orientation: TViewportInfo["orientation"] = width / height < 1 ? "portrait" : "landscape";

	return {
		width,
		height,
		orientation
	};
}


function updateRootRule (key: string, value: string | number): void {
	document.documentElement.style.setProperty(key, value.toString());
}

export { getScrollbarWidth, getViewport, updateRootRule };
