import React, {useEffect, useState, Fragment} from "react";
import {TSingleInput} from "@app-types/TSingleInput";
import MultiSelect, {multiSelectToOptions, TMultiSelectOption} from "../../ui/MultiSelect/MultiSelect";
import {useTranslation} from "react-i18next";
import {Label} from "../../ui/Typography";
import "./style.scss";
import {get} from "../../libs";
import PendingIcon from "../../ui/Icon/PendingIcon";
import ReadonlyInput from "../../ui/Input/ReadonlyInput";

type Props = Omit<TSingleInput, "value"> & {
	value: {
		types: string[],
		items: string[]
	}
}

type TSuppliesItem = {
	code: string,
	name: string
}

const SuppliersSelectorField = ({ value, onChange}: Props) => {
	const [pending, setPending] = useState<boolean>(false);
	const [suppliesItems, setSupplies] = useState<TMultiSelectOption[]>([]);

	const { types = [], items = [] } = value;
	const { t } = useTranslation();

	const suppliesTypes: TMultiSelectOption[] = [
		{ value: "fuel", longName: t("Fuels items"), shortName: t("Fuels" )},
		{ value: "goods", longName: t("Goods items"), shortName: t("Goods" )},
		{ value: "service", longName: t("Services items"), shortName: t("Services") }
	];

	useEffect(() => {
		let isCanceled = false;
		setPending(true);

		get<TSuppliesItem[]>({ endpoint: "/transactions/supplies", params: { limit: 999, type: types  }})
			.then(res => {

				const multiOptions: TMultiSelectOption[] = res.data.map(item => ({
					value: item.code,
					shortName: item.name,
					longName: item.name,
				}));

				if(!isCanceled) {
					setSupplies(multiOptions);
					setPending(false);
				}
			});

		return () => {
			isCanceled = true;
		};
	}, [types, types.length]);


	const suppliersTypesOptions = multiSelectToOptions(types, suppliesTypes);
	const suppliersItemsOptions = multiSelectToOptions(items, suppliesItems);

	const changeHandler = (key: keyof Props["value"]) => (selectValue: string[]) => {
		onChange({ ...value, [key]: selectValue });
	};

	return (
		<div className="c-suppliers-field">
			<Label as="span">{ t("Supply type") }</Label>
			<MultiSelect onChange={changeHandler("types")} options={{
				optionsList: suppliesTypes,
				noSelectedLabel: t("No types selected"),
				allSelectedLabel: t("All types selected"),
				placeholder: t("Select supplies"),
				dropdownKey: "supplier_types_dropdown",
				selectAllButton: true,
			}} value={suppliersTypesOptions} />

			{
				types.length > 0 && (
					<Fragment>
						<Label as="span">{ t("Supplies") }</Label>
						{
							pending
								? <PendingIcon />
								: suppliesItems.length > 0
									? (
										<MultiSelect onChange={changeHandler("items")} options={{
											optionsList: suppliesItems,
											noSelectedLabel: t("No supplies selected"),
											allSelectedLabel: t("All supplies selected"),
											placeholder: t("Select supplies"),
											dropdownKey: "supplies_items_dropdown",
											selectAllButton: true
										}} value={suppliersItemsOptions}/>
									)
									: <ReadonlyInput value={t("No supplies for group")}/>
						}
					</Fragment>
				)

			}
		</div>
	);
};

export default SuppliersSelectorField;
