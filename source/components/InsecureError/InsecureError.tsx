import React, {useEffect} from "react";
import "./styles.scss";
import View from "../View";
import {H1, Paragraph} from "../../ui/Typography";


const InsecureError = () => {

	useEffect(() => {
		if(process.env.B2B_PORTAL) {
			window.location.href = process.env.B2B_PORTAL;
		}
	}, []);

	return (
		<View>
			<H1>{ window.__app__static__i18n__.pageError }</H1>
			<Paragraph>{ window.__app__static__i18n__.redirectToPortal }</Paragraph>
		</View>
	);
};

export default InsecureError;
