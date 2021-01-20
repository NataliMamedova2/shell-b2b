import React, {useEffect, useState} from "react";
import "./styles.scss";
import Popup from "../../../ui/Popup";
import Text, { Label } from "../../../ui/Typography";
import {formatDate, get, printFormattedSum} from "../../../libs";
import {useTranslation} from "react-i18next";
import Button from "../../../ui/Button";

const LOAD_LIMIT = 100;

type Props = {
	onClose: () => void
}

type TDiscountItem = {
	id: string,
	sum: number,
	date: Date
}

type LoaderProps = { offset: number, onLoad: (d: TDiscountItem[]) => void }

const ICON_BONUS_URL: string = "/media/icon-bonus.svg";

const DiscountItem = ({ item }: { item: TDiscountItem }) => {
	return (
		<div className="c-discount-history__item">
			<Text className="c-discount-history__date" type="link">{ formatDate({date: item.date, formatKey: "date"}) }</Text>
			<Text className="c-discount-history__value" type="link">{printFormattedSum(item.sum)}</Text>
		</div>
	);
};

const DiscountItemsLoader = ({ offset, onLoad }: LoaderProps) => {
	useEffect(() => {
		get<TDiscountItem[]>({ endpoint: "/discounts", params: {
				limit: LOAD_LIMIT,
				offset: offset
			}})
			.then(({data}) => onLoad(data))
			.catch(err => console.error(err));
	}, [offset, onLoad]);

	return null;
};

const DiscountHistory = ({ onClose}: Props) => {
	const [ items, setItems ] = useState<TDiscountItem[]>([]);
	const [ offset, setOffset ] = useState<number>(0);
	const [ noMore, setNoMore ] = useState<boolean>(false);
	const [ pending, setPending ] = useState<boolean>(true);
	const { t } = useTranslation();

	const onloadHandler = (data: TDiscountItem[]) => {
		setItems([...items, ...data]);
		setPending(false);
		if(data.length < LOAD_LIMIT) {
			setNoMore(true);
		}
	};

	const initLoadItems = () => {
		setPending(true);
		setOffset(offset + LOAD_LIMIT);
	};

	return (
		<Popup size="search" onClose={onClose} className="c-discount-history">
			<DiscountItemsLoader offset={offset} onLoad={onloadHandler} />

			<div className="c-discount-history__header">

				<img className="c-discount-history__icon" src={ICON_BONUS_URL} alt=" "/>
				<Label>{ t("History of discount charges") }</Label>
			</div>
			<div className="c-discount-history__stick">
				<Label className="c-discount-history__date">{ t("Date")  }</Label>
				<Label className="c-discount-history__value">{ t("Sum") }</Label>
			</div>
			<div className="c-discount-history__list">
				{
					items.map((item) => <DiscountItem key={item.id} item={item} />)
				}

				{
					noMore
						? null
						: (
							<div className="c-discount-history__load">
								<Button
									type="primary"
									pending={pending}
									onClick={initLoadItems}
								>
									{ pending ? t("Loading") : t("Load more") }
								</Button>
							</div>
						)
				}
			</div>
		</Popup>
	);
};

export default DiscountHistory;
