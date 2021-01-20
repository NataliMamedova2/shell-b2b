import React, {useEffect, useReducer} from "react";
import "./styles.scss";
import MultiSelect from "../../../ui/MultiSelect";
import {ACTION_SELECT_ITEM, ACTION_SELECT_TYPE, initialStatsState, statsReducer, statsTypes} from "./statsReducer";
import {
	initialItemsStatsState,
	itemsStatsReducer,
	STATS_ITEMS_ERROR,
	STATS_ITEMS_FETCH,
	STATS_ITEMS_SUCCESS,
} from "./itemsStatsReducer";
import {get} from "../../../libs";
import axios from "axios";
import {getTmcItems, prepareTmcItems} from "./helpers";
import {StatsCardsInfoView, StatsCardsMessage, StatsCardsNoTypeSelected} from "./parts";
import {multiSelectToValues} from "../../../ui/MultiSelect/MultiSelect";
import classNames from "classnames";


const StatsCardsWidget = () => {
	const [stats, dispatchStats] = useReducer(statsReducer, initialStatsState);
	const [tmc, dispatchTmc] = useReducer(itemsStatsReducer, initialItemsStatsState);

	useEffect(() => {
		dispatchTmc({ type: STATS_ITEMS_FETCH });

		axios.all([
			get({endpoint: "/fuel/search"}),
			get({endpoint: "/goods/search"}),
			get({endpoint: "/services/search"}),
		]).then(res => {
			const [ fuels, goods, services ] = res.map(i => i.data);

			const items = {
				fuels: prepareTmcItems(fuels, "fuels"),
				goods: prepareTmcItems(goods, "goods"),
				services: prepareTmcItems(services, "services"),
			};
			const filteredTmcItems = getTmcItems(items, stats.type);

			dispatchTmc({ type: STATS_ITEMS_SUCCESS, items });
			dispatchStats({
				type: ACTION_SELECT_ITEM,
				value: multiSelectToValues(filteredTmcItems),
				allItems: filteredTmcItems
			});

		}).catch(e => {
				dispatchTmc({ type: STATS_ITEMS_ERROR });
			});
	}, [stats.type]);

	const itemsOptionsList = getTmcItems(tmc.items, stats.type);

	const changeTypeHandler = (value: string[]) => {
		dispatchStats({
			type: ACTION_SELECT_TYPE,
			value: value,
			allItems: itemsOptionsList
		});
	};

	const changeItemsHandler = (value: string[]) => {
		dispatchStats({
			type: ACTION_SELECT_ITEM,
			value: value,
			allItems: itemsOptionsList
		});
	};

	const noOptionsSelected = stats.type.length === 0 || stats.items.length === 0;
	const classes = classNames("c-cards-stats", {
		"is-pending": tmc.pending
	});

	return (
		<div className={classes}>

			<div className="c-cards-stats__toolbar">
				<MultiSelect onChange={changeTypeHandler} options={{
					optionsList: statsTypes,
					noSelectedLabel: "No fuels selected",
					allSelectedLabel: "all selected",
					placeholder: "Select fuels",
					dropdownKey: "fuel_dropdown",
					selectAllButton: true
				}} value={stats.type} />

				{
					stats.type.length > 0
					? (
							<MultiSelect onChange={changeItemsHandler} options={{
								optionsList: itemsOptionsList,
								noSelectedLabel: "No services selected",
								allSelectedLabel: "all selected",
								placeholder: "Select services",
								dropdownKey: "services_dropdown",
								selectAllButton: true
							}} value={stats.items}/>
						)
						: <StatsCardsNoTypeSelected />
				}
			</div>

			<div className="c-cards-stats__view">
				{
					noOptionsSelected
						? <StatsCardsMessage message="No options selected. Select one or more options for get the statistic." />
						: <StatsCardsInfoView items={stats.items} />
				}
			</div>
		</div>
	);
};

export default StatsCardsWidget;
