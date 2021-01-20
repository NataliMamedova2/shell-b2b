import React, {ReactNode} from "react";
import "./styles.scss";
import PagePatch from "../../../components/PagePatch";

type Props = {
	children?: ReactNode,
	id?: string
}

const TicketsEdit = ({children, id, ...props}: Props) => {
	return (
		<PagePatch page="Tickets Edit" />
	);
};

TicketsEdit.defaultProps = {};

export default TicketsEdit;
