import React, {ReactNode} from "react";
import "./styles.scss";
import PagePatch from "../../../components/PagePatch";

type Props = {
	children?: ReactNode
}

const TicketsCreate = ({children, ...props}: Props) => {
	return (
		<PagePatch page="Tickets Create" />
	);
};

export default TicketsCreate;
