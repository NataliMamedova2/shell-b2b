import React, {useState} from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import {Label, Note} from "../../../ui/Typography";
import Select from "../../../ui/Select";
import ReadonlyInput from "../../../ui/Input/ReadonlyInput";
import NumberInput from "../../../ui/Input/NumberInput";
import ErrorsTooltip from "../../../components/Field/ErrorsTooltip";
import {printFormattedSum} from "../../../libs";
import classNames from "classnames";
import {useTranslation} from "react-i18next";

type TFuelFieldInput = Omit<TSingleInput, "value"> & {
	value: {
		id: string,
		volume: number,
		meta: {
			price: number,
			selectedTitle: string
		}
	}
}

const InvoiceBySuppliersField = ({value, errors, onChange, options}: TFuelFieldInput) => {
	const { t } = useTranslation();
	const [ fuelSelected, setFuelSelected ] = useState<boolean>(false);
	const { selectOptions = {}, defaultLabel, prices } = options;
	const { volume, id } = value;
	const price = prices ? prices[id] : null;
	const volumeErrors = errors ? errors["volume"] : null;
	const idErrors = errors ? errors["id"] : null;
	const classes = classNames("c-fuel-input", {
		"is-error": volumeErrors || idErrors
	});

	const changeVolumeHandler = (val: string) => {
		onChange({
			...value,
			volume: parseInt(val),
		});
	};

	const selectFuelHandler = (val: string) => {
		setFuelSelected(true);

		onChange({
			id: val,
			volume: 0,
			meta: {
				price: prices[val],
				selectedTitle: selectOptions[val]
			}
		});
	};

	const printPrice = (maybePrice: number) => maybePrice ? `${maybePrice / 100} ${t("uah/l")}` : "--.--";
	const printSum = (maybePrice: number) => maybePrice ?  printFormattedSum(maybePrice * volume) : "--.--";

	return (
		<div className={classes}>

			<div className="c-fuel-input__col">
				<div className="c-field">
					<span className="c-field__label">
						{ idErrors && <ErrorsTooltip errors={idErrors}/> }
						<Label as="span">{ t("Inventory") }</Label>
					</span>
					{
						fuelSelected
							? (
								<ReadonlyInput value={value.meta.selectedTitle}/>
							)
							: (
								<Select value={id} onChange={selectFuelHandler} options={{ selectOptions, defaultLabel  }} />
							)
					}
					{ idErrors && <Note className="c-field__error">{ t("Error")}</Note> }
				</div>

			</div>
			<div className="c-fuel-input__col">
				<div className="c-field">
					<span className="c-field__label">
						{ volumeErrors && <ErrorsTooltip errors={volumeErrors}/> }
						<Label as="span">{ t("Volume, l") }</Label>
					</span>
					{
						fuelSelected
							? (
								<NumberInput value={volume} onChange={changeVolumeHandler} options={{ placeholder: "Volume, l", maxLength: 5}} />
							)
							: <ReadonlyInput value="0"/>
					}
					{ volumeErrors && <Note className="c-field__error">{  t("Error")}</Note> }
				</div>
			</div>
			<div className="c-fuel-input__col">
				<div className="c-field">
					<span className="c-field__label">
						<Label as="span">{ t("Price")}</Label>
					</span>
					<ReadonlyInput value={printPrice(price)}/>
				</div>
			</div>
			<div className="c-fuel-input__col">
				<div className="c-field">
					<span className="c-field__label">
						<Label>{ t("Sum") }</Label>
					</span>
					<ReadonlyInput value={printSum(price)}/>
				</div>
			</div>
		</div>
	);
};

export default InvoiceBySuppliersField;
