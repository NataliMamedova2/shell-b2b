import React, { useEffect } from "react";
import { formatDate } from "../../../libs";
import { Caption } from "../../../ui/Typography";
import "./styles.scss";
import {useTranslation} from "react-i18next";
import {observer} from "mobx-react";
import appSystemStore from "../../../stores/AppSystemStore";

type Props = {
	at: number | Date
}

const ObserveTimeUpdate = observer(() => {

	const { lastSystemUpdate, lastSystemUpdatePending } = appSystemStore;

	if(lastSystemUpdatePending) {
		return <Caption>...</Caption>;
	}

	if(!lastSystemUpdate) {
		return <Caption color="error">Error. Can't get time update</Caption>;
	}

	return <Caption>{ formatDate({ date: lastSystemUpdate, formatKey: "timedate" }) }</Caption>;
});

const UpdatedAt = ({at}: Props) => {
	const { t } = useTranslation();

	useEffect(() => {
		appSystemStore.initialFetchLastSystemUpdate();
	},[]);

	return (
		<div className="c-updated-at">
			<Caption>{ t("Recent update of system") }:</Caption>
			<ObserveTimeUpdate />
		</div>
	);
};

export default UpdatedAt;
