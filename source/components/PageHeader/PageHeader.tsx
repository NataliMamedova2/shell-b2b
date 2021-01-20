import React, {ReactNode} from "react";
import "./styles.scss";
import Back from "../../ui/Back";
import {H1, Paragraph} from "../../ui/Typography";

type Props = {
	title?: string,
	back?: string,
	lead?: string,
	children?: ReactNode
}

const PageHeader = ({title, lead, back, children}: Props) => {
	return (
		<div className="c-page-header">
			<div className="c-page-header__section">
				{ back && <Back to={back} /> }

				<div className="c-page-header__content">
					{ title && <H1 className="c-page-header__title">{title}</H1>}
					{ lead && <Paragraph className="c-page-header__title">{lead}</Paragraph> }
				</div>
			</div>
			<div className="c-page-header__actions">
				{children}
			</div>
		</div>
	);
};

export default PageHeader;
