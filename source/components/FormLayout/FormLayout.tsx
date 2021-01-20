import React, {ReactNode} from "react";
import "./styles.scss";
import Back from "../../ui/Back";
import {TIconType} from "@app-types/TIconType";
import PageIcon from "../PageIcon";
import {H1, Paragraph} from "../../ui/Typography";
import {useTranslation} from "react-i18next";

type Props = {
	children: ReactNode
}

type InfoProps = {
	icon: TIconType,
	title: string,
	text?: string
}

const FormLayout = ({children}: Props) => {
	return (
		<div className="c-form-layout">
			{children}
		</div>
	);
};

const FormLayoutAside = ({children}: Props) => <div className="c-form-layout__aside">{children}</div>;
const FormLayoutMain = ({children}: Props) => <div className="c-form-layout__main">{children}</div>;
const FormLayoutInfo = ({icon, text, title}: InfoProps) => {
	const { t } = useTranslation();
	return (
		<div className="c-form-layout__info">
			<PageIcon type={icon}/>
			<H1>{ t(title)}</H1>
			{ text && <Paragraph>{ t(text) }</Paragraph> }
		</div>
	);
};

const FormLayoutInfoWrapper = ({children}: Props) => <div className="c-form-layout__info-wrapper">{children}</div>;

const FormLayoutBack = ({to}: { to: string }) => (
	<div className="c-form-layout__back">
		<Back to={to} />
	</div>
);


export { FormLayout, FormLayoutAside, FormLayoutMain, FormLayoutInfo, FormLayoutBack, FormLayoutInfoWrapper };
