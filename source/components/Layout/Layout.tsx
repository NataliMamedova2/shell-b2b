import React, {ReactNode} from "react";
import Header, {ClearHeader} from "../Header";
import Footer from "./Footer";
import Navigation, { NavigationMobile } from "./Navigation";
import { Breakpoint } from "../../libs/Breakpoint";
import "./styles.scss";
import {observer} from "mobx-react";
import appUIStore from "../../stores/AppUIStore";
import {RouteComponentProps, withRouter} from "react-router-dom";
import classNames from "classnames";
import ManagerCard from "../ManagerCard";
import ErrorBoundary from "../BoundaryError";

type Props = {
	children: ReactNode
} & RouteComponentProps

const clearLayoutPathname = [
	"/error/not-found"
];

const Layout = ({children, ...props}: Props) => {

	const { pathname } = props.location;
	const isClearView = clearLayoutPathname.includes(pathname);
	const classes = classNames("m-layout", {
		"has-no-sidebar": isClearView
	});

	return (

			<div className={classes}>
				<div className="m-layout__header">
					<ErrorBoundary moduleName="Header">
					{
						!isClearView
							? <Header />
							: <ClearHeader underline />
					}
					</ErrorBoundary>
				</div>

				<Breakpoint range={["mobile", "small"]}>
					{ appUIStore.isNavActive && <NavigationMobile /> }
				</Breakpoint>

				<ErrorBoundary moduleName="navigation">
					{ !isClearView && (
						<div className="m-layout__sidebar">
							<ManagerCard type="default" />
							<Navigation />
						</div>
					)}
				</ErrorBoundary>

				<div className="m-layout__main">
					<div className="m-layout__view">
						<ErrorBoundary moduleName="Layout view">
							{children}
						</ErrorBoundary>
					</div>
					<ErrorBoundary moduleName="Footer">
						{ !isClearView && <Footer /> }
					</ErrorBoundary>

				</div>
			</div>
	);
};

export default withRouter(observer(Layout));
