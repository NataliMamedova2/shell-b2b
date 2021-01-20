import React from "react";
import { H2, H4, Paragraph } from "../../ui/Typography";
import "./styles.scss";
import View from "../View";
import Button from "../../ui/Button";

type Props = {
	page: string,
	home?: boolean
}

const PagePatch = ({page, home = false}: Props) => {
	return (
		<View className="c-page-patch">
			<img src="/media/under-construction.svg" alt=" " width={100} height={100}/>
			<H4>[{page}]</H4>
			<H2>Page is under development</H2>
			<Paragraph>Ask your PM for a current status of development.</Paragraph>
			<Paragraph>Stay tuned. It will be soon...</Paragraph>

			{ !home && <Button to="/">Go Home</Button> }

		</View>
	);
};

export default PagePatch;
