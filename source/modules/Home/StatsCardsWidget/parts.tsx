import React, {useEffect, useState} from "react";
import {Label, Paragraph} from "../../../ui/Typography";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import Tooltip from "../../../ui/Tooltip/Tooltip";
import {printFormattedSum} from "../../../libs";
import {TMultiSelectOption} from "../../../ui/MultiSelect/MultiSelect";
import {useTranslation} from "react-i18next";

type TCardsInfo = {
	day: number,
	week: number,
	month: number,
}

const StatsCardsMessage = ({ message }: { message: string }) => (
	<div className="c-cards-stats__message">
		<Paragraph>{message}</Paragraph>
	</div>
);

const StatsCardsPending = () => (
	<div className="c-cards-stats__pending">
		<PendingIcon/>
	</div>
);

const StatsCardsInfo = ({data}: {data: Partial<TCardsInfo> }) => {
	const { t }  = useTranslation();

	return (
		<div className="c-cards-stats__data">
		<span className="c-cards-stats__col">
			<Label className="c-cards-stats__label">
				{ t("Day") }
				<Tooltip message="daily stats, from 00:00" tooltipKey="cards-stats-day" size="medium" />
			</Label>
			<Paragraph>{ data.day ? printFormattedSum(data.day) : "" }</Paragraph>
		</span>
			<span className="c-cards-stats__col">
			<Label className="c-cards-stats__label">
				{ t("Week") }
				<Tooltip message="weekly stats, from monday" tooltipKey="cards-stats-week" size="medium" />
			</Label>
			<Paragraph>{ data.week ? printFormattedSum(data.week) : "" }</Paragraph>
		</span>
			<span className="c-cards-stats__col">
			<Label className="c-cards-stats__label">
				{ t("Month") }
				<Tooltip message="monthly stats, from 1 day of month" tooltipKey="cards-stats-month" size="medium" />
			</Label>
			<Paragraph>{ data.month ? printFormattedSum(data.month) : "" }</Paragraph>
		</span>
		</div>
	);
};

const StatsCardsNoTypeSelected = () => (
	<Paragraph>Select at least one type</Paragraph>
);

const StatsCardsInfoView = ({ items }: { items: TMultiSelectOption[] }) => {
	const { t } = useTranslation();
	const [ data, setData ] = useState<Partial<TCardsInfo>>();
	const [ pending, setPending ] = useState<boolean>(true);
	const FAKE_DATA: TCardsInfo = { day: 10000, week: 99999, month: 200000 };

	useEffect(() => {
		setPending(true);
		const timeout = setTimeout(() => {
			setData( FAKE_DATA);
			setPending(false);
		}, 500);

		return () => {
			clearTimeout(timeout);
		};
	}, [FAKE_DATA, items]);


	if(pending) {
		return <StatsCardsPending />;
	}

	if(!data) {
		return <StatsCardsMessage message={ t("Not found data for the request") }  />;
	}

	return <StatsCardsInfo data={data} />;
};

export { StatsCardsMessage, StatsCardsInfoView, StatsCardsNoTypeSelected };
