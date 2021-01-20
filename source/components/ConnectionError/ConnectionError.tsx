import React from "react";
import "./styles.scss";
import ReactDOM from "react-dom";
import {H1} from "../../ui/Typography";

const ConnectionError = () => {
	return (
		<div className="m-offline">
			<H1 color="light">{ window.__app__static__i18n__.offlineMessage }</H1>
		</div>
	);
};

const initConnectionListeners = (overlayRoot: HTMLElement | null) => {
	window.onoffline = () => ReactDOM.render(<ConnectionError />, overlayRoot);

	window.ononline = () => {
		if (overlayRoot) ReactDOM.unmountComponentAtNode(overlayRoot);
	};

};

export { initConnectionListeners };
