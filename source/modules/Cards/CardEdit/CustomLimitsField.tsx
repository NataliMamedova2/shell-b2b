import React from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import Search from "../../../components/Search";
import SingleField from "../../../components/Field/SingleField";
import Text from "../../../ui/Typography";
import limitsStore from "../limitsStore";
import {TSearchOnSelectPayload} from "../../../components/Search/Search";
import {useTranslation} from "react-i18next";
import {createSearchCache} from "../../../components/Search/useSearch";

type TFuelFieldInput = Omit<TSingleInput, "value"> & {
	value: {
		name: string,
		dayLimit: number,
		weekLimit: number,
		monthLimit: number,
	},
	errors: any
}

const DAY_LIMIT_KEY = "dayLimit";
const WEEK_LIMIT_KEY = "weekLimit";
const MONTH_LIMIT_KEY = "monthLimit";

const customLimitsCache = createSearchCache();

const CustomLimitsField = ({value, options, onChange, errors}: TFuelFieldInput) => {
	const {
		labelsPostfix,
		searchTitle,
		searchEndpoint,
		searchExtractKey,
		storeKey
	} = options;

	const { t } = useTranslation();

	const selectHandler = (key: string) => ({ data, id }: TSearchOnSelectPayload) => {
		onChange({ ...value, [key]: data, id });
	};
	const changeHandler = (key: string) => (data: string) => {
		onChange({ ...value, [key]: data });
	};
	const filterHandler = (data: TSearchOnSelectPayload[]) => data.filter((item: TSearchOnSelectPayload) => !limitsStore.hasSelected(storeKey, item.id));

	const isNameSelected = value && value.name;
	const showSearch = value !== null && !value.name;
	const maybeErrors = (key: string) =>  errors ? errors[key] : null;
	const maybeValue = (key: keyof TFuelFieldInput["value"]) => value && value[key] ? value[key].toString() : "0";

	return (
		<div className="c-limits-field">
			<Text as="span" type="link" className="c-limits-field__title">{ isNameSelected ? value.name : "- -"}</Text>
			{ showSearch && (
				<Search
					cache={customLimitsCache}
					onCancel={() => onChange(null)}
					onSelect={selectHandler("name")}
					endpoint={searchEndpoint}
					title={searchTitle}
					extractId={"id"}
					extractKey={searchExtractKey}
					filterHandler={filterHandler}
				/>
			)}
			<div className="c-limits-field__items">
				<div className="c-limits-field__col">
					<SingleField
						value={maybeValue(DAY_LIMIT_KEY)}
						errors={maybeErrors(DAY_LIMIT_KEY)}
						onChange={changeHandler(DAY_LIMIT_KEY)}
						options={{}}
						label={ t("Day") + ", " + labelsPostfix}
						type="InputNumber" />
				</div>

				<div className="c-limits-field__col">
					<SingleField
						errors={maybeErrors(WEEK_LIMIT_KEY)}
						value={maybeValue(WEEK_LIMIT_KEY)}
						onChange={changeHandler(WEEK_LIMIT_KEY)}
						options={{}}
						label={ t("Week") + ", " + labelsPostfix}
						type="InputNumber" />
				</div>

				<div className="c-limits-field__col">
					<SingleField
						value={maybeValue(MONTH_LIMIT_KEY)}
						errors={maybeErrors(MONTH_LIMIT_KEY)}
						onChange={changeHandler(MONTH_LIMIT_KEY)}
						options={{}}
						label={ t("Month") + ", " + labelsPostfix}
						type="InputNumber" />
				</div>
			</div>
		</div>
	);
};

export default CustomLimitsField;
