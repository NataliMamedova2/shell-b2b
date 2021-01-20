import React, {ReactNode } from "react";
import "./styles.scss";
import PagePatch from "../../../components/PagePatch";

type Props = {
	children?: ReactNode
}


const NotificationsList = ({children, ...props}: Props) => {
	return (
		<PagePatch page="Notifications List" />
	);
};

export default NotificationsList;
