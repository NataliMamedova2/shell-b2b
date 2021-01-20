import React from "react";
import "./styles.scss";
import { Caption, Paragraph } from "../../../ui/Typography";
import Icon from "../../../ui/Icon";
import {useTranslation} from "react-i18next";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import {TLabelsMapping} from "../Filters";
import get from "lodash-es/get";

type FilterItem = string | string[];

type Props = {
	items: {
		[key: string]: FilterItem
	},
	itemsData: {
		[key: string]: any
	}
	labelsMapping: TLabelsMapping,
	onRemove: any,
	pending: boolean
}

const maybeName = (data: any) => (key: string) => {
	const target = data.filter((el: any) => el.code === key)[0];
	return get(target, "name", key);
};

const getItemTitle = (itemKey: string, itemValue: any, itemsData: any,): string => {
	const shouldExtractNameKeys = ["supplies", "networkStations", "regions"];

	if(shouldExtractNameKeys.includes(itemKey)) {
		return itemValue.map(maybeName(itemsData[itemKey])).join(" | ");
	}

	return  typeof itemValue === "string" ? itemValue : itemValue.join(", ");
};


const createLabel = ( mapping: TLabelsMapping, config: { filterKey: string, filterValue: any, itemsData: any }): string => {
	const { prefixes, translates } = mapping;
	const { filterKey, filterValue, itemsData } = config;
	const translatesMapping = get(translates, filterKey );

	const title = translatesMapping
		? get(translatesMapping, filterValue, filterValue)
		: getItemTitle(filterKey, filterValue, itemsData);
	const prefix = get(prefixes, filterKey);

	return [prefix, title].filter(Boolean).join(": ");
};

const FiltersApplied = ({ pending, onRemove, itemsData, items, labelsMapping }: Props) => {
	const { t } = useTranslation();

	return (
		<div className="c-applied-filters">
			{
				pending && (
					<div className="c-applied-filters__pending">
						<PendingIcon/>
					</div>
				)
			}

			<Paragraph className="c-applied-filters__title">{t("Filters")}:</Paragraph>
			<div className="c-applied-filters__list">

				{
					Object.keys(items).map((filterKey) => {
						const label = createLabel(labelsMapping, {
							filterValue: get(items, filterKey),
							filterKey,
							itemsData
						});

						return (
							<div key={filterKey} className="c-applied-filters__item">
								<Caption>{ label }</Caption>
								<Icon onClick={() => onRemove(filterKey)} type="close" />
							</div>
						);
					})
				}
			</div>
		</div>
	);
};

export default FiltersApplied;
