import React from "react";
import { Paragraph } from "../../../ui/Typography";
import "./styles.scss";

type Props = {
	name?: string
}

const CompanyName = ({name}: Props) => {


	return (
		<div className="c-company-name">
			<Paragraph>{name}</Paragraph>
		</div>
	);
};

export default CompanyName;
