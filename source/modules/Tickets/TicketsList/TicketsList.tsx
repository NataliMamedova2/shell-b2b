import React, {ReactNode} from "react";
import "./styles.scss";
import PagePatch from "../../../components/PagePatch";

type Props = {
	children?: ReactNode
}

const TicketsList = ({children, ...props}: Props) => {
	return (
		<PagePatch page="Tickets List" />
	);
};

TicketsList.defaultProps = {};

export default TicketsList;
