import React, {Fragment, ReactNode} from "react";
import {Redirect} from "react-router";
import { check } from "./check";
import rules from "../../config/rbac-rules";
import AccessDeniedError from "../../modules/Error/AccessDeniedError";
import profileStore from "../../modules/Users/profileStore";

type TCheck = {
	perform: string,
	data?: any,
}

type Props = {
	children?: ReactNode,
	use?: ReactNode,
	redirect?: string
} & TCheck;

const Can = ({ perform, data, redirect, use, children, ...props}: Props) => {
	const hasAccess = check({ rules, role: profileStore.userRole, action: perform, data });

	if(hasAccess && children) {
		return (
			<Fragment>
				{
					React.Children.map(children, (child: ReactNode) => {
						if(React.isValidElement(child)) {
							return typeof child.type === "string"
								? React.cloneElement(child, {...child.props, ...props})
								: child;
						}
						if(typeof child === "string") {
							return child;
						}
						return null;
					})
				}
			</Fragment>
		);
	}

	if(redirect) {
		return <Redirect to={redirect} />;
	}

	if(use) {
		return <Fragment>{use}</Fragment>;
	}

	if(use === null) {
		return null;
	}

	return <AccessDeniedError/>;

};


const useCan = (perform: string): boolean  => {
	return check({ rules, role: profileStore.userRole, action: perform });
};

export { useCan };
export default Can;
