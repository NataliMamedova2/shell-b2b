import {TSingleInput} from "@app-types/TSingleInput";
import MultiSelect, {multiSelectToOptions, TMultiSelectOption} from "../../../ui/MultiSelect/MultiSelect";
import React, {useEffect, useState} from "react";
import {get} from "../../../libs";
import PendingIcon from "../../../ui/Icon/PendingIcon";

type Props = Omit<TSingleInput, "value"> & {
	value: string[]
}

type TRegionItem = {
	code: string,
	name: string
}

const RegionSelectorField = ({value, options, onChange, error}: Props) => {
	const [pending, setPending] = useState<boolean>(true);
	const [regions, setRegions] = useState<TMultiSelectOption[]>([]);

	useEffect(() => {
		let isCanceled = false;
		setPending(true);

		get<TRegionItem[]>({ endpoint: "/transactions/regions", params: { limit: 999 }})
			.then(res => {

				const multiOptions: TMultiSelectOption[] = res.data.map(item => ({
					value: item.code,
					shortName: item.name,
					longName: item.name,
				}));

				if(!isCanceled) {
					setRegions(multiOptions);
					setPending(false);
				}
			});

		return () => {
			isCanceled = true;
		};
	}, []);

	if(pending) {
		return <PendingIcon/>;
	}
	const regionsValue: TMultiSelectOption[] = multiSelectToOptions(value, regions);

	const fieldOptions = {
		optionsList: regions,
		noSelectedLabel: "No regions selected",
		allSelectedLabel: "All regions",
		placeholder: "Select regions",
		dropdownKey: "regions_field_dropdown",
		selectAllButton: true
	};

	return (
		<MultiSelect
			onChange={onChange}
			options={fieldOptions}
			value={regionsValue} />
	);
};

export { RegionSelectorField };
