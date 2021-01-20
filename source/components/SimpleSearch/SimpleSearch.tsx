import React, {SyntheticEvent, useEffect, useState} from "react";
import {useTranslation} from "react-i18next";
import Icon from "../../ui/Icon";
import "./styles.scss";

type TSimpleSearch = {
	onSearch: (data: string) => void,
	onReset: (data: string) => void,
	validateValue?: (value: string) => string,
	placeholder: string,
	initValue: string
}

const SimpleSearch = ({ placeholder, onSearch, onReset, validateValue, initValue = "" }: TSimpleSearch ) => {

	const [query, setQuery] = useState<string>(initValue);
	const { t } = useTranslation();

	useEffect(() => {
		if(initValue) {
			setQuery(initValue);
		}

		return () => {
			setQuery("");
		};
	}, [initValue]);

	const isInitialState = initValue === query;

	const changeHandler = (e: any) => {
		const targetValue = e.target.value;
		const validatedValue = validateValue ? validateValue(targetValue) : targetValue;
		setQuery(validatedValue);
	};
	const clearHandler = () => {
		setQuery("");
		onReset("");
	};

	const submitHandler = (e: SyntheticEvent) => {
		e.preventDefault();
		e.stopPropagation();

		if(!isInitialState) {
			onSearch(query);
		}
	};

	return (
		<form className="c-simple-search" onSubmit={submitHandler}>
			<div className="c-simple-search__field">
				<input value={query} type="text" placeholder={placeholder} onChange={changeHandler} className="c-simple-search__input"/>
				{ query && query.length > 0 && <Icon type="close"  onClick={clearHandler} /> }
			</div>

			<button className="c-button c-button--primary" type="submit" disabled={ isInitialState }>
				<span className="c-button__label">{t("Search")}</span>
			</button>
		</form>
	);
};

export default React.memo(SimpleSearch);
