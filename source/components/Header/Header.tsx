import React from "react";
import "./styles.scss";
import Logo from "../Logo";
import CompanyName from "../Layout/CompanyName";
import UpdatedAt from "../Layout/UpdatedAt";
import LangSelector from "../Layout/LangSelector";
import { Breakpoint } from "../../libs/Breakpoint";
import Icon from "../../ui/Icon";
import appUIStore from "../../stores/AppUIStore";
import { observer } from "mobx-react";
import classNames from "classnames";
import profileStore from "../../modules/Users/profileStore";

const Header = () => {
	return (
		<div className="m-header">
			<div className="m-header__section">
				<Logo />
				<Breakpoint range={["tablet", "large"]}>
					<CompanyName name={ profileStore.myCompany.name } />
				</Breakpoint>
			</div>
			<div className="m-header__section">
				<Breakpoint range={["tablet", "large"]}>
					<UpdatedAt at={Date.now()} />
				</Breakpoint>
				{/*<NotificationsBadge />*/}
				<LangSelector />
				<Breakpoint only={["mobile", "tablet", "small"]}>
					<div className="m-header__burger" onClick={appUIStore.toggleNav(!appUIStore.isNavActive)}>
						<Icon type={appUIStore.isNavActive ? "close" : "burger"} />
					</div>
				</Breakpoint>
			</div>
		</div>
	);
};

type ClearHeaderProps = {
	underline?: boolean
}

const ClearHeader = ({underline = false }: ClearHeaderProps) => {
	return (
		<div className={classNames("m-header", {
			"has-underline": underline
		})}>
			<div className="m-header__section">
				<Logo />
			</div>
			<div className="m-header__section">
				<LangSelector />
			</div>
		</div>
	);
};

export { ClearHeader };
export default observer(Header);
