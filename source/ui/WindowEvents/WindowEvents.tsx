import {useEffect, memo} from "react";
import {logger} from "../../libs";

type Props = {
	onClick?: () => void,
	onScroll?: () => void,
	onResize?: () => void,
}

const WindowEvents = ({onClick, onScroll, onResize}: Props) => {

	useEffect(() => {
		if(onClick) window.addEventListener("click", onClick);
		if(onScroll) window.addEventListener("scroll", onScroll);
		if(onResize) window.addEventListener("resize", onResize);

		return () => {
			logger("WindowEvents unmount", {onClick: !!onClick, onScroll: !!onScroll, onResize: !!onResize});
			if (onClick) window.removeEventListener("click", onClick);
			if (onScroll) window.removeEventListener("scroll", onScroll);
			if (onResize) window.removeEventListener("resize", onResize);

		};
	},[]);

	return null;
};

export default memo(WindowEvents);
