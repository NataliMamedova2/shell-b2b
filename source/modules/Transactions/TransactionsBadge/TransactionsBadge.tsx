import React, {ReactNode} from "react";
import "./styles.scss";
import Badge from "../../../ui/Badge";

type Props = {
	children?: ReactNode
}

const TransactionsBadge = ({children, ...props}: Props) => {
	return (
		<Badge type="darkgrey" size="small">11</Badge>
	);
};

export default TransactionsBadge;
