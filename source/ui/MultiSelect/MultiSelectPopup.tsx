import React, {useState} from "react";
import {Label, Paragraph} from "../Typography";
import Button from "../Button";
import Popup from "../Popup";
import classNames from "classnames";
import {TMultiSelectOption} from "./MultiSelect";
import {useTranslation} from "react-i18next";

type Props = {
	onSubmit: (val: TMultiSelectOption[]) => void
	onCancel: () => void,
	allOptions: TMultiSelectOption[],
	onAll?: (values: TMultiSelectOption[]) => void
	onNone?: () => void
	selectedOptions: TMultiSelectOption[],
	title: string
}

const MultiSelectPopup = ({onCancel, onSubmit, selectedOptions, allOptions, title}: Props) => {
	const { t } = useTranslation();
	const [selected, setSelected] = useState<TMultiSelectOption[]>(selectedOptions);

	const changeHandler = (target: TMultiSelectOption) => {
		if(isActive(target)) {
			setSelected([...selected.filter(item => item.value !== target.value)]);
		} else {
			setSelected([...selected, target]);
		}
	};

	const isActive = (target: TMultiSelectOption): boolean => selected.findIndex(item => item.value === target.value) !== -1;

	return (
		<Popup onClose={onCancel} size="search">

			<div className="c-multi-select__popup">
				<div className="c-multi-select__header">
					<Label className="c-search__label">{ title }</Label>
				</div>

				<div className="c-multi-select__body">
					{
						allOptions.map(option => {
							const classes = classNames("c-multi-select__option", { "is-active": isActive(option)});
							return (
								<div className={classes} key={option.value} onClick={() => changeHandler(option)} role="button">
									<span className="c-multi-select__icon" />
									<Paragraph>{option.longName}</Paragraph>
								</div>
							);
						})
					}
				</div>
				<div className="c-multi-select__actions">
					<Button onClick={() => onSubmit(selected)} type="primary">{ t("Submit") }</Button>
					<Button onClick={onCancel} type="ghost">{ t("Cancel") }</Button>
				</div>

			</div>

		</Popup>
	);
};

export default MultiSelectPopup;
