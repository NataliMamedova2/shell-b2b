import React from "react";
import Button from "../../../ui/Button";
import {Paragraph, H4} from "../../../ui/Typography";
import "./styles.scss";
import { useTranslation } from "react-i18next";

const NotFoundError = () => {
	const { t } = useTranslation();
	return (
		<div className="m-error">
			<div className="m-error__content">
				<div className="m-error__code">
					<div className="m-error__wrapper">
						<span className="m-error__num m-error__num--4"> </span>
						<span className="m-error__num m-error__num--0"> </span>
						<span className="m-error__num m-error__num--4"> </span>
					</div>
				</div>
				<div className="m-error__info">
					<div className="m-error__message">

						<H4>{t("Page not found")}</H4>
						<Paragraph>
							{ t("... The page you requested may have been moved or deleted. You may also have made a small typo when typing the address - even with us;)") }
						</Paragraph>
						<Button to="/">
							{ t("To home page") }
						</Button>
					</div>
				</div>
			</div>
		</div>
	);
};

export default NotFoundError;
