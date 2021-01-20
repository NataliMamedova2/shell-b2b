import React, {ReactNode, Component, Fragment } from "react";
import "./styles.scss";
import PagePatch from "../../../components/PagePatch";
import PageTitle from "../../../components/PageTitle";

type UserActionsHistoryProps = {
	children?: ReactNode
}

class UserActionsHistory extends Component<UserActionsHistoryProps>{

	render() {
		return (
			<Fragment>
				<PageTitle contentString={"User Actions History"} />
				<PagePatch page="User Actions History" />
			</Fragment>
		);
	}

	static defaultProps = {};
}

export default UserActionsHistory;
