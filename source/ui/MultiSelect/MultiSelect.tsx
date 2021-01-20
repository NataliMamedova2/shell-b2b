import React, {useRef, Fragment, useEffect} from "react";
import "./styles.scss";
import classNames from "classnames";
import { observer } from "mobx-react";
import { TSingleInput } from "@app-types/TSingleInput";
import Icon from "../Icon";
import MultiSelectPopup from "./MultiSelectPopup";
import MultiSelectDropdown from "./MultiSelectDropdown";
import appUIStore from "../../stores/AppUIStore";
import { useBreakpoint } from "../../libs/Breakpoint";

export type TMultiSelectOption = {
	value: string,
	shortName: string,
	longName: string,
	type?: string
}

export type TMultiSelectFieldInput = Omit<TSingleInput, "value"> & {
	value: TMultiSelectOption[]
}

/**
 * ["fuel"] -> [{ value: "fuel", shortName: "Fuel", "longName": "Fuel" }]
 */
export const multiSelectToOptions = (value: string[], allOptions: TMultiSelectOption[]): TMultiSelectOption[] => {
	return value.map(val => {
		const item = allOptions.filter(option => option.value === val)[0];
		return {...item};
	}).filter(i => i.value);
};

/**
 * [{ value: "fuel", shortName: "Fuel", "longName": "Fuel" }] -> ["fuel"]
 */
export const multiSelectToValues = (selected: TMultiSelectOption[]) => selected.map(i => i.value);

const computeVisibleValue = (
	value: TMultiSelectOption[],
	optionsList: TMultiSelectOption[],
	allSelectedLabel: string,
	noSelectedLabel: string) => {
	return value.length > 0
		? value.length < optionsList.length ? value.map(i => i.shortName).join(", ") : allSelectedLabel
		: noSelectedLabel ? noSelectedLabel : "Press to open select menu";
};

const EscapeListener = ({ onEscape }: { onEscape: () => void }) => {

	const escFunction = (e: KeyboardEvent) => {
		if(e.key === "Esc" || e.key === "Escape") {
			onEscape();
		}
	};

	useEffect(() => {
		document.addEventListener("keydown", escFunction, false);

		return () => {
			document.removeEventListener("keydown", escFunction, false);
		};
	});

	return null;
};

const EnterListener = ({ onEnter }: { onEnter: () => void }) => {

	const escFunction = (e: KeyboardEvent) => {
		if(e.key === "Enter" || e.key === "Enter") {
			onEnter();
		}
	};

	useEffect(() => {
		document.addEventListener("keydown", escFunction, false);

		return () => {
			document.removeEventListener("keydown", escFunction, false);
		};
	});

	return null;
};

const MultiSelect = ({value, onChange, options}: TMultiSelectFieldInput) => {
	/**
	 * current string key of dropdown
	 * method for set or clear key
	 */
	const { currentOverLayer, setOverLayer } = appUIStore;

	/**
	 * dropdownKey - is required
	 */
	const {
		optionsList = [],
		noSelectedLabel,
		allSelectedLabel,
		placeholder = "Multi choices",
		dropdownKey,
		selectAllButton
	} = options;

	const { state: { acrossMobileTablet } } = useBreakpoint();

	/** Create ref for pass to dropdown component (use for position with getBoundClientRect) */
	const fieldRef: any = useRef(null);

	/** Instance key to current key */
	const isOpen = currentOverLayer === dropdownKey;

	/**  Helper for define is `target` exists in selected array  */
	const isActive = (target: TMultiSelectOption): boolean => value.findIndex(item => item.value === target.value) !== -1;

	/** Generate String value for show to user: `array`, no-selected label, all-selected label */
	const visibleValue = computeVisibleValue(value, optionsList, allSelectedLabel, noSelectedLabel);

	/** Alias - close current dropdown */
	const closeHandler = () => setOverLayer(null);
	const openHandler = () => setOverLayer(dropdownKey);

	/** `submitHandler` is change event for Popup mod */
	const submitHandler = (selected: TMultiSelectOption[]) => {
		onChange(multiSelectToValues(selected));
		closeHandler();
	};

	/** `changeHandler` is change event for Dropdown mode */
	const changeHandler = (target: TMultiSelectOption) => {
		if(isActive(target)) {
			onChange(multiSelectToValues([...value.filter(item => item.value !== target.value)]));
		} else {
			const nextValues = multiSelectToValues([...value, target]);
			const nextOptions = multiSelectToValues(optionsList).filter(item => nextValues.includes(item));

			onChange(nextOptions);
		}
	};

	const selectAllHandler = (targets: TMultiSelectOption[]) => {
		onChange(multiSelectToValues([...targets]));
	};
	const selectNoneHandler = () => onChange([]);

	const selectAllHandlersProps = selectAllButton
		? {
			onAll: selectAllHandler,
			onNone: selectNoneHandler,
			isAllSelected: value.length === optionsList.length
		} : {};

	const wrapperClasses = classNames("c-multi-select", { "is-open": isOpen });

	return (
		<div className={wrapperClasses} role="button">
			<EscapeListener onEscape={closeHandler} />
			<EnterListener onEnter={closeHandler} />
			<div className="c-multi-select__field" onClick={openHandler} ref={fieldRef}>
				{visibleValue}
				<Icon type={ isOpen ? "triangle-up" : "triangle-down"} />
			</div>

			{
				isOpen && (
					<Fragment>
						{
							acrossMobileTablet
								? (
									<MultiSelectPopup
										title={placeholder}
										onSubmit={submitHandler}
										onCancel={closeHandler}
										allOptions={optionsList}
										selectedOptions={value}
										{...selectAllHandlersProps}
									/>
								)
								: (
									<MultiSelectDropdown
										isActive={(v) => isActive(v)}
										stickRef={fieldRef}
										onChange={changeHandler}
										onCancel={closeHandler}
										allOptions={optionsList}
										selectedOptions={value}
										{...selectAllHandlersProps}
									/>
								)
						}
					</Fragment>
				)
			}
		</div>
	);
};


export default observer(MultiSelect);
