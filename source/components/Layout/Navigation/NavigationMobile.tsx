import React, { Component } from "react";
import "./styles.scss";
import Navigation from "./Navigation";
import appUIStore from "../../../stores/AppUIStore";
import UpdatedAt from "../UpdatedAt";
import ManagerCard from "../../ManagerCard";
import ScrollDisable from "../../../ui/ScrollDisable";

class NavigationMobile extends Component {
	render() {
		return (
			<div className="m-navigation-mobile">
				<div className="m-navigation-mobile__overlay" onClick={appUIStore.toggleNav(false)} />
				<div className="m-navigation-mobile__wrapper">
					<ManagerCard type="mobile"/>
					<Navigation />
					<UpdatedAt at={Date.now()}/>
				</div>
				<ScrollDisable/>
			</div>

		);
	}
}

export { NavigationMobile };
