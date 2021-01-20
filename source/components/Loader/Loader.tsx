import React from "react";
import Icon from "../../ui/Icon";
import {H2} from "../../ui/Typography";
import "./styles.scss";

type Props = {
	message?: string
}

const Loader = ({ message }: Props) => {
	return (
		<div className="c-loader">
			<Icon type="pending" pending={true} />
			{ message && <H2 className="c-loader__message">{ message }</H2> }
		</div>
	);
};

export default Loader;
