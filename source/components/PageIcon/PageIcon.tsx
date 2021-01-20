import React from "react";
import "./styles.scss";
import Icon from "../../ui/Icon";
import { TIconType } from "@app-types/TIconType";

type Props = {
	type: TIconType
}

const PageIcon = ({type}: Props) => {
	return (
		<div className="c-page-icon">
			<Icon type={type} />
		</div>
	);
};

export default PageIcon;
