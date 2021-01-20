import React from "react";
import { H4 } from "../../ui/Typography";

import { NavLink } from "react-router-dom";
import "./styles.scss";

const StaticLogo = () => (
	<div className="c-logo is-static">
		<img src="/media/logo.svg" className="c-logo__image" alt="shell logo" />
		<H4>Business</H4>
	</div>
);

const Logo = () => {
	return (
		<NavLink
			exact
			to="/"
			className="c-logo"
			activeClassName="is-active"
		>
			<img src="/media/logo.svg" className="c-logo__image" alt="shell logo" />
			<H4>Business</H4>
		</NavLink>
	);
};

export { StaticLogo };
export default Logo;
