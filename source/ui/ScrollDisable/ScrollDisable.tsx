import {useEffect} from "react";
import "./styles.scss";

const ScrollDisable = () => {

	useEffect(() => {
		document.body.classList.add("is-frozen");

		return () => {
			const popups = document.querySelectorAll(".c-popup") || [];
			if(popups.length === 0) {
				document.body.classList.remove("is-frozen");
			}
		};
	}, []);

	return null;
};

export default ScrollDisable;
