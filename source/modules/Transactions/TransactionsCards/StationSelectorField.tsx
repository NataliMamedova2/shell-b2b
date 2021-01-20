import React, {useEffect, useState} from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import Search, {TSearchOnSelectPayload} from "../../../components/Search/Search";
import {useTranslation} from "react-i18next";
import Button from "../../../ui/Button";
import Icon from "../../../ui/Icon";
import { Caption } from "../../../ui/Typography";
import { createSearchCache } from "../../../components/Search/useSearch";
import {createStationsCache} from "./createStationsCache";
import transactionsCardsStore from "../transactionsCardsStore";

const selectedStationCache = createStationsCache();
const stationCache = createSearchCache();

type Props = Omit<TSingleInput, "value"> & {
	value: string[]
}

const StationSelectorField = ({value, onChange}: Props) => {
	const { t } = useTranslation();
	const [searchShown, showSearch] = useState(false);
	const [ selected, setSelected ] = useState<{ id: string, data: string }[]>([]);

	useEffect(() => {
		selectedStationCache.add(value);
		return () => {
			selectedStationCache.clear();
		};
	}, [value]);

	useEffect(() => {
		const metaInfo = transactionsCardsStore.list.metaInfo;
		const itemsData = metaInfo ? metaInfo.filters.networkStations : {};
		const setupSelected = value.map((itemKey: string) => {
			const itemIndex = itemsData.findIndex((i: any) => i.code === itemKey);

			if(itemIndex === -1) {
				return {
					id: itemKey,
					data: itemKey
				};
			}
			return {
				id: itemsData[itemIndex].code,
				data: itemsData[itemIndex].name
			};
		});

		setSelected(setupSelected);
	}, []);

	const extractId = (item: any) => item.id;

	const selectHandler = (items: TSearchOnSelectPayload | TSearchOnSelectPayload[]) => {
		const selectedItems: any[] = Array.isArray(items) ? items : [items];
		const nextItems: any[] = [...selected, ...selectedItems];
		const nextIds = nextItems.map(extractId);

		onChange(nextIds);
		setSelected(nextItems);
		showSearch(false);
	};

	const filterHandler = (data: TSearchOnSelectPayload[]) => {
		return data.filter((item: TSearchOnSelectPayload) => !selectedStationCache.has(item.code));
	};
	const removeHandler = (id: string) => {
		const nextItems = [...selected].filter((item) => item.id !== id);
		const nextIds = nextItems.map(extractId);

		onChange(nextIds);
		setSelected(nextItems);
		selectedStationCache.remove(id);
	};

	const showSearchHandler = () => showSearch(true);
	const hideSearchHandler = () => showSearch(false);

	return (
		<div className="c-station-selector">

			<div className="c-station-selector__list">
				{
					selected.map((item) => {
						return (
							<div className="c-station-selector__item" key={item.id}>
								<Caption>{item.data}</Caption>
								<Icon type="close" onClick={() => removeHandler(item.id)} />
							</div>
						);
					})
				}
			</div>

			<Button type="paper" onClick={showSearchHandler} icon="add-square">
				{ t("Add stations") }
			</Button>

			{ searchShown && (
				<Search
					filterHandler={filterHandler}
					onCancel={hideSearchHandler}
					onSelectMulti={selectHandler}
					cache={stationCache}
					multi={true}
					title={t("Network stations")}
					endpoint="/transactions/network-stations"
					extractId="code"
					extractKey="name"
				/>
			)}
		</div>
	);
};
export default StationSelectorField;
