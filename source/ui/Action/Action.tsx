import React from "react";
import "./styles.scss";
import Icon from "../Icon";
import { Note } from "../Typography";
import {TIconType} from "@app-types/TIconType";

type Props = {
	icon: TIconType,
	title: string,
	onClick: () => void
}

const Action = ({ icon, title, onClick }: Props) => {
	return (
		<div role="button" className="c-action" onClick={onClick}>
			<Icon type={icon}  />
			<Note>{ title }</Note>
		</div>
	);
};

export default Action;
