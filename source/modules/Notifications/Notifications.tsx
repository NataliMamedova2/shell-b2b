import React, {ReactNode} from "react";
import NotificationsList from "./NotificationsList";
import Can from "../../components/Can";

type Props = {
	children?: ReactNode
}

const Notifications = ({children, ...props}: Props) => {
	return (
		<Can perform="notifications:list">
			<NotificationsList />
		</Can>
	);
};


export default Notifications;
