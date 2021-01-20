import React, {useState, Fragment, useEffect} from "react";
import Popup from "../../ui/Popup";
import "./styles.scss";
import Button from "../../ui/Button";
import {H4, Label, Paragraph} from "../../ui/Typography";
import useDebounce from "./useDebounce";
import {useTranslation} from "react-i18next";

import PendingIcon from "../../ui/Icon/PendingIcon";
import classNames from "classnames";
import Icon from "../../ui/Icon";
import {TSearchCache, useSearch} from "./useSearch";
import {logger} from "../../libs";

type Props = {
	title: string,
	multi?: boolean,
	onEmpty?: () => void,
	emptyActionLabel?: string
	onCancel: () => void,
	onSelect?: (item : TSearchOnSelectPayload) => void,
	onSelectMulti?: (items : TSearchOnSelectPayload[]) => void,
	endpoint: string,
	extractId: string,
	extractKey: string,
	filterHandler?: (d: any) => any,
	renderResultLabel?: (data: any) => string
	cache?: TSearchCache
}

export type TSearchOnSelectPayload = {
	data: string,
	[id: string]: string
}

export type TSearchOnSelectHandler = (item: TSearchOnSelectPayload) => void

type TResultsProps = {
	filterResults?: (d: any) => any,
	onSelect: TSearchOnSelectHandler,
	[property: string]: any
}

type TResultListProps = {
	multi?: boolean,
	items: TResultsProps[],
	extractId: string,
	extractKey: string,
	selectedItems: TSearchOnSelectPayload[],
	selectHandler: TSearchOnSelectHandler
	renderResultLabel?: (data: any) => string
}

const ResultPending = () => {
	const { t } = useTranslation();
	return (
		<div className="c-search__pending">
			<H4 color="darkgrey">{ t("Looking for") }</H4>
			<PendingIcon/>
		</div>
	);
};

const ResultEmpty = ({ query }: { query: string }) => {
	const { t } = useTranslation();
	return (
		<div className="c-search__empty">
			<H4 color="darkgrey">{ t("No results for") }</H4>
			<Label>{query}</Label>
		</div>
	);
};

const ResultList = ({ renderResultLabel, multi = false, extractId, extractKey, items, selectedItems, selectHandler }: TResultListProps) => {
	const printResult = (item: any) => renderResultLabel ? renderResultLabel(item) : item[extractKey];

	return (
		<Fragment>
			{
				items.map(item => {
					const isActive = selectedItems.findIndex(elem => elem.id === item[extractId]) !== -1;
					const itemClasses = classNames("c-search__item", {
						"is-active": isActive,
						"is-multi": multi
					});
					return (
						<div className={itemClasses} key={item[extractId]} role="button" onClick={() => selectHandler({ data: item[extractKey], id: item[extractId]  })}>
							{
								multi && <Icon type={isActive ? "checkbox-checked" : "checkbox-unchecked"} />
							}
							<Paragraph>{ printResult(item) }</Paragraph>
						</div>
					);
				})
			}
		</Fragment>
	);
};

const Search = (
	{
		extractId = "id",
		multi = false,
		renderResultLabel,
		extractKey,
		title,
		endpoint,
		filterHandler,
		onCancel,
		onEmpty,
		onSelect,
		onSelectMulti,
		emptyActionLabel,
		cache
	}: Props) => {
	const [selected, setSelected] = useState<TSearchOnSelectPayload[]>([]);
	const [query, setQuery] = useState<string>("");
	const debouncedQuery = useDebounce(query, 300);
	const { pending, data } = useSearch(endpoint, debouncedQuery, cache);
	const { t } = useTranslation();

	useEffect(() => {
		setSelected([]);
	}, [pending]);

	const clearQuery = () => setQuery("");

	const selectHandler: TSearchOnSelectHandler = ({ data, id }) => {

		if(multi) {
			const index = selected.findIndex((item: any) => item.id === id);
			if(index === -1) {
				setSelected([...selected, { data, id }]);
			} else {
				setSelected([...selected.filter((item: any) => item.id !== id)]);
			}
		} else  {
			setSelected([{ data, id }]);
		}
	};

	const submitHandler = () => {
		if(multi) {
			if(onSelectMulti ) {
				onSelectMulti(selected as TSearchOnSelectPayload[]);
			}
		} else  {
			if(onSelect) {
				onSelect(selected[0] as TSearchOnSelectPayload);
			}
		}
	};

	const noSelectedItems = selected.length === 0;

	const filteredResults = filterHandler ? filterHandler(data) : data;
	const emptyResults = !pending && filteredResults.length === 0;

	logger("search result", { data, filteredResults });

	return (
		<Popup size="search" onClose={onCancel}>

			<div className="c-search">
				<div className="c-search__header">
					<Label className="c-search__label">{ title }</Label>
				</div>
				<div className="c-search__field">
					<input className="c-search__input" placeholder={t("Search")} onChange={(e) => setQuery(e.target.value)} value={query} type="text" />
					{
						debouncedQuery.length > 0 && <Button onClick={clearQuery} type="ghost" className="c-search__clear">{t("Clear")}</Button>
					}
				</div>

				{
					<div className="c-search__results">
						{
							pending
								? <ResultPending />
								: emptyResults
									? <ResultEmpty query={query}/>
									: <ResultList renderResultLabel={renderResultLabel} multi={multi} items={filteredResults} extractId={extractId} extractKey={extractKey} selectedItems={selected} selectHandler={selectHandler}/>
						}
					</div>
				}
				<div className="c-search__actions">
					{
						!emptyResults
							? (
								<Fragment>
									<Button onClick={submitHandler} type="primary" disabled={noSelectedItems}>{ t("Submit") }</Button>
									<Button onClick={onCancel} type="ghost">{ t("Cancel") }</Button>
								</Fragment>
							) : (
								<Button
									className="c-search__empty-action"
									onClick={ onEmpty ? onEmpty : clearQuery }
									type="ghost">
									{ onEmpty && emptyActionLabel ? emptyActionLabel : t("Clear") }
								</Button>
							)
					}
				</div>
			</div>

		</Popup>
	);
};

export default Search;
