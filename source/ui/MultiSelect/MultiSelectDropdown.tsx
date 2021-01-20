import React, {SyntheticEvent, useEffect, useLayoutEffect, useRef, useState} from "react";
import SimplePortal from "../SimplePortal";
import WindowEvents from "../WindowEvents";
import classNames from "classnames";
import {Paragraph} from "../Typography";
import {TMultiSelectOption} from "./MultiSelect";
import {useTranslation} from "react-i18next";
import appUIStore from "../../stores/AppUIStore";
import Button from "../Button";

type Props = {
	stickRef: any,
	onChange: (val: TMultiSelectOption) => void
	onCancel: () => void,
	onAll?: (values: TMultiSelectOption[]) => void
	onNone?: () => void
	isAllSelected?: boolean
	allOptions: TMultiSelectOption[],
	selectedOptions: TMultiSelectOption[],
	isActive: (option: TMultiSelectOption) => boolean
}

const getCurrentPosition = (stickNode: HTMLElement, listHeight = 0) => {
	const stickRect = stickNode.getBoundingClientRect();
	const distanceToScreenBottom = window.innerHeight - stickRect.bottom - 10;
	const dropTopPosition = distanceToScreenBottom >= listHeight
		? stickRect.bottom
		: (stickRect.top - listHeight);

	return {
		left: `${stickRect.left}px`,
		width: `${stickRect.width}px`,
		bottom: "auto",
		top: `${dropTopPosition}px`
	};
};


const MultiSelectDropdown = ({stickRef, onCancel, onChange, onNone, onAll, allOptions, isActive, isAllSelected}: Props) => {
	const listRef = useRef<HTMLDivElement>(null);
	const [position, setPosition] = useState<any>(null);
	const { t } = useTranslation();

	useEffect(() => {
		const inside = document.querySelector(".c-filters-form .c-form__groups");

		if(inside) {
			inside.addEventListener("scroll", appUIStore.resetOverLayer);
		}
		document.addEventListener("mousedown", outsideClickHandler, false);

		return () => {
			document.removeEventListener("mousedown", outsideClickHandler, false);
			if(inside) {
				inside.removeEventListener("scroll", appUIStore.resetOverLayer, false);
			}
		};
	}, []);

	useLayoutEffect(() => {
		updatePosition();
	}, []);

	const getListHeight = () => listRef.current ? listRef.current.getBoundingClientRect().height : 210;

	const outsideClickHandler = (e: Event) => {
		if( listRef && listRef.current && listRef.current.contains(e.target as Node) ) {
			return;
		}
		appUIStore.resetOverLayer();
	};

	const selectAllHandler = () => onAll && onAll(allOptions);

	const selectNoneHandler = () => onNone && onNone();

	const okHandler = (e: SyntheticEvent) => {
		e.stopPropagation();
		e.preventDefault();
		onCancel();
	};

	const updatePosition = () => setPosition(getCurrentPosition(
		stickRef.current,
		getListHeight()
	));

	return (
		<SimplePortal>
			<WindowEvents onScroll={updatePosition} onClick={updatePosition} onResize={appUIStore.resetOverLayer}  />
			<div className="c-multi-select__dropdown" ref={listRef} style={{ ...position }}>
				<div className="c-multi-select__body">

					{
						allOptions.map(option => {
							const classes = classNames("c-multi-select__option", { "is-active": isActive(option)});
							return (
								<div className={classes} key={option.value} onClick={() => onChange(option)} role="button">
									<span className="c-multi-select__icon" />
									<Paragraph>{option.longName}</Paragraph>
								</div>
							);
						})
					}
				</div>
				{
					(onAll && onNone) && (
						<div className="c-multi-select__dropdown-footer">

							<div
								className={classNames("c-multi-select__option", "c-multi-select__option--all", { "is-active": isAllSelected})}
								onClick={isAllSelected ? selectNoneHandler : selectAllHandler }
								role="button"
							>
								<span className="c-multi-select__icon" />
								<Paragraph>{ isAllSelected ? t("Unselect all") : t("Select all") }</Paragraph>
							</div>
							<Button type="ghost" onClick={okHandler}>{ t("Ok") }</Button>
						</div>
					)
				}
			</div>
		</SimplePortal>
	);
};

export default MultiSelectDropdown;
