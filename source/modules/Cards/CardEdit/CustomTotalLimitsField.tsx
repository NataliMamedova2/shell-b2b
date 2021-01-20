import React from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import SingleField from "../../../components/Field/SingleField";
import {Label} from "../../../ui/Typography";
import {useTranslation} from "react-i18next";

type TFuelFieldInput = Omit<TSingleInput, "value"> & {
	value: {
		day: number,
		week: number,
		month: number,
	},
	errors: any
}

const DAY_KEY = "day";
const WEEK_KEY = "week";
const MONTH_KEY = "month";

const CustomTotalLimitsField = ({value, onChange, errors}: TFuelFieldInput) => {
	const { t } = useTranslation();
	const changeHandler = (key: string) => (inputValue: any) => {
		onChange({ ...value, [key]: inputValue });
	};

	const maybeErrors = (key: string) =>  errors ? errors[key] : null;
	const maybeValue = (key: keyof TFuelFieldInput["value"]) => value && value[key] ? value[key].toString() : "0";

	return (
		<div className="c-limits-field c-limits-field--clear">
			<Label className="c-limits-field__title">{ t("Total limit") }</Label>

			<div className="c-limits-field__items">

				<div className="c-limits-field__col">
					<SingleField
						errors={maybeErrors(DAY_KEY)}
						value={maybeValue(DAY_KEY)}
						onChange={changeHandler(DAY_KEY)}
						options={{}}
						label={`${t("Day")}, ${ t("uah") }`}
						type="InputNumber" />
				</div>

				<div className="c-limits-field__col">
					<SingleField
						errors={maybeErrors(WEEK_KEY)}
						value={maybeValue(WEEK_KEY)}
						onChange={changeHandler(WEEK_KEY)}
						options={{}}
						label={`${t("Week")}, ${ t("uah") }`}
						type="InputNumber" />
				</div>

				<div className="c-limits-field__col">
					<SingleField
						errors={maybeErrors(MONTH_KEY)}
						value={maybeValue(MONTH_KEY)}
						onChange={changeHandler(MONTH_KEY)}
						options={{}}
						label={`${t("Month")}, ${ t("uah") }`}
						type="InputNumber" />
				</div>
			</div>
		</div>
	);
};

export default CustomTotalLimitsField;
