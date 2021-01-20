import React from "react";
import "./styles.scss";
import { Link, useHistory} from "react-router-dom";
import Icon from "../Icon";
import {useTranslation} from "react-i18next";


type Props = {
	to?: string,

};

const Back = ({ to }: Props) => {
	const history = useHistory();
	const { t } = useTranslation();
	const title = t("Go to prev page");

	if(to) {
		return <Link className="c-back" to={to} title={title}><Icon type="chevron-left" /></Link>;
	}

	return (
		<div className="c-back" role="button" onClick={history.goBack} title={title}>
			<Icon type="chevron-left" />
		</div>
	);
};

export default Back;
