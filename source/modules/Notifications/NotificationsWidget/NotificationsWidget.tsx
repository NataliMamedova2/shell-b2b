import React, {ReactNode} from "react";
import "./styles.scss";
import Button from "../../../ui/Button";

type Props = {
	children?: ReactNode
}

const NotificationsWidget = ({children, ...props}: Props) => {
	return (
		<Button to="/notifications">12 notifications</Button>
	);
};

export default NotificationsWidget;
