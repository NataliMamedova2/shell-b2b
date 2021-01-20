import React, { Fragment } from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import Text, {Note, Paragraph} from "../../../ui/Typography";
import Input from "../../../ui/Input";
import { printFormattedSum} from "../../../libs";
import {useTranslation} from "react-i18next";

type TCustomBillField = Omit<TSingleInput, "value"> & {
	value: {
		type: "credit" | "custom",
		amount: number,
		creditAmount: number
	}
}

const CustomRatio = ({ valueType, type, onChange }: { valueType: string, type: string, onChange: (value: string) => void }) => {
	return (
		<input
			checked={valueType === type}
			type="radio"
			value={type}
			className="c-bill-field__native"
			onChange={(e) => onChange(e.target.value)}
		/>
	);
};

const prepareAmount = (val: string) => {
	const cleanVal = val.toString().replace(/\D/g, "").replace(/^0/, "");

	if(!val || !cleanVal) {
		return "0";
	}
	return cleanVal;
};

const InvoiceByDebtField = ({value, options, onChange }: TCustomBillField) => {
	const { hideDebtInput } = options;
	const { t } = useTranslation();

	const changeHandler = (key: string) => (inputValue: string) => {
		onChange({
				...value,
			[key]: key === "amount" ? prepareAmount(inputValue) : inputValue,
		});
	};

	const focusHandler = () => changeHandler("type")("custom");

	return (
		<div className="c-bill-field">

			{
				!hideDebtInput
					? (
						<label className="c-bill-field__item">
							<CustomRatio valueType={value.type} type="credit" onChange={(value) => changeHandler("type")(value)}/>
							<span className="c-bill-field__icon" role="button" />
							<Text className="c-bill-field__label" as="span" type="link" color="darkgrey">{ t("Credit debt") }</Text>
							<Paragraph className="c-bill-field__credit-value">{ printFormattedSum(value.creditAmount) }</Paragraph>
						</label>
					)
					: null
			}

			<label className="c-bill-field__item">
				<CustomRatio valueType={value.type} type="custom" onChange={changeHandler("type")}/>
				<span className="c-bill-field__icon" role="button" />
				<Text className="c-bill-field__label" as="span" type="link" color="darkgrey" >{t("Another amount")}</Text>
				<span className="c-bill-field__input">
					{

						value.type === "custom"
							? (
								<Fragment>
									<Input
										value={value.amount.toString()}
										onFocus={focusHandler}
										onChange={changeHandler("amount")}
										options={{ type: "text", maxLength: 15 }} />

									<span className="c-bill-field__sign">
										{value.amount}<span className="c-bill-field__currency">{ t("uah") }</span>
									</span>
								</Fragment>
							)
							: (
								<Input value="" onChange={() => {}} options={{ disabled: true }} />
							)

					}
				</span>
				<Note>{ t("Enter the amount you want to create an invoice") }</Note>
			</label>
		</div>
	);
};

export default InvoiceByDebtField;
