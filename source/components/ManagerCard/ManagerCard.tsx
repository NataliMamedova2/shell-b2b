import React, {ReactNode, useState} from "react";
import Text, { Caption, Paragraph, Note } from "../../ui/Typography";
import classNames from "classnames";
import "./styles.scss";
import Icon from "../../ui/Icon";
import {TIconType} from "@app-types/TIconType";
import {useTranslation} from "react-i18next";
import { observer } from "mobx-react";
import profileStore from "../../modules/Users/profileStore";

type Props = {
	children?: ReactNode,
	type: "mobile" | "widget" | "default"
}

type ContactItemProps = {
	href: string,
	hrefPrefix: string,
	icon: TIconType | null
}

const ContactItem = ({ href, hrefPrefix, icon }: ContactItemProps) => {

	const classes = classNames("c-manager-card__item", {
		"is-button": icon
	});

	return (
		<Text className={classes} type="link" as="a" href={"".concat(hrefPrefix,href)}>
			{ icon && <Icon type={icon} /> }
			<span className="c-manager-card__item-href">{href}</span>
		</Text>
	);
};

const ManagerCard = ({children, type, ...props}: Props) => {
	const { t } = useTranslation();
	const [active, setActive] = useState(false);

	const classes = classNames("c-manager-card", {
		[`c-manager-card--${type}`]: type,
		"is-active": active
	});

	const isEmailIcon = type === "mobile" || type === "widget";
	const isPhoneIcon = type === "mobile";
	const isCollapsible = type === "mobile";
	const isHidden = type !== "mobile" || active;

	const { name, email, avatar, phone } = profileStore.myManager;
	const { contractDate, contractNumber } = profileStore.myCompany;

	return (
		<div className={classes}>

			<div className="c-manager-card__intro">
				<div className="c-manager-card__info">
					<Caption color="disable">{ t("Your manager") }</Caption>
					<Paragraph className="c-manager-card__name">{ name }</Paragraph>
				</div>

				<img className="c-manager-card__avatar" src={avatar} alt={name}/>

				{
					isCollapsible && (
						<div className="c-manager-card__button" onClick={() => setActive(!active)}>
							<Icon type="chevron-down" />
						</div>
					)
				}
			</div>

			{
				isHidden && (
					<div className="c-manager-card__contacts">
						<ContactItem href={phone} hrefPrefix="tel:" icon={ isPhoneIcon ? "phone" : null }  />
						<ContactItem href={email} hrefPrefix="mailto:" icon={ isEmailIcon ? "email" : null } />

						{
							(contractNumber && contractDate) && (
								<Note className="c-manager-card__contract" color="disable">{ t("Contract №") } { contractNumber } { t("from") } { contractDate }</Note>
							)
						}
					</div>
				)
			}
		</div>
	);
};

export default observer(ManagerCard);
