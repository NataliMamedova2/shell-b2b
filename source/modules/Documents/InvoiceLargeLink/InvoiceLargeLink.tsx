import React, {ReactNode} from "react";
import "./styles.scss";
import PageIcon from "../../../components/PageIcon";
import Icon from "../../../ui/Icon";
import {H5} from "../../../ui/Typography";
import classNames from "classnames";
import {TIconType} from "@app-types/TIconType";
import {Link} from "react-router-dom";

type Props = {
	icon: TIconType,
	title: string,
	to?: string,
}

type WrapperProps = {
	children: ReactNode,
	className: string,
	to: string
}


const StaticWrapper = ({ ...props }: Omit<WrapperProps, "to">) => {
	const { children, className } = props;
	return <div className={className}>{children}</div>;
};

const LinkWrapper = ({  ...props }: WrapperProps ) => {
	const { children, to, className } = props;
	return <Link className={className} to={to}>{children}</Link>;
};

const InvoiceLargeLink = ({to, icon, title}: Props) => {
	const classes = classNames("c-bill-link", {
		"is-static": !to
	});

	const Wrapper: { (props: WrapperProps): JSX.Element } = to ? LinkWrapper : StaticWrapper;

	return (
		<Wrapper className={classes} to={"/documents/invoice/" + to}>
			<PageIcon type={icon}/>
			<H5>{ title }</H5>
			{ to && <Icon type="chevron-right" />}
		</Wrapper>
	);
};

export default InvoiceLargeLink;
