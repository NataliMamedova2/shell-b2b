import React from "react";
import Button from "../../../ui/Button";
import "./styles.scss";
import {Breakpoint} from "../../../libs/Breakpoint";
import tPlural from "../../../config/i18n/tPlural";
import {useTranslation} from "react-i18next";

const defaultProps = {
	count: 12
};

type Props = typeof defaultProps;

const NotificationsBadge = ({count = 0}: Props) => {
	const { t } = useTranslation();

	const tNotifications = tPlural(count, {
		one: t("notifications_1", { defaultValue: "notification" }),
		two: t("notifications_2", { defaultValue: "notifications" }),
		five: t("notifications_5", { defaultValue: "notifications" })
	});

	return (
		<Button className="c-notifications-badge" to="/notifications">
			{count}
			<Breakpoint range={["tablet", "large"]}>
				{ tNotifications }
			</Breakpoint>
		</Button>
	);
};

NotificationsBadge.defaultProps = defaultProps;

export default React.memo(NotificationsBadge);
