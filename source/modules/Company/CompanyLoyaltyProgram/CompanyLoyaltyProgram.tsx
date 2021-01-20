import React, {ReactNode} from "react";
import "./styles.scss";
import PagePatch from "../../../components/PagePatch";

type Props = {
	children?: ReactNode
}

const CompanyLoyaltyProgram = ({children, ...props}: Props) => {
	return (
		<PagePatch page="Loyalty Program" />
	);
};

export default CompanyLoyaltyProgram;
