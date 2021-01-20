import React from "react";
import "./styles.scss";

type Props = {
	data: any
}

const JSONCode = ({ data }: Props) => {
	return (
		<code className="c-json-code">
			{ JSON.stringify(data, null, 4) }
		</code>
	);
};

export default JSONCode;
