import React from "react";
import {observer} from "mobx-react";
import {IFiltersStore, TFiltersData} from "../../stores/FiltersStore/FiltersStore";
import {TSimpleFormConfigFactory, TSimpleFormData} from "@app-types/TSimpleForm";
import FiltersForm from "./FiltersForm";
import FiltersApplied from "./FiltersApplied";
import Button from "../../ui/Button";
import "./styles.scss";
import {useTranslation} from "react-i18next";
import {IReportStore} from "../../stores/ReportStore/ReportStore";
import PopupAlert from "../../ui/Popup/PopupAlert";
import PendingIcon from "../../ui/Icon/PendingIcon";

export type TLabelsMapping = {
	prefixes: { [key: string]: string },
	translates: { [key: string]: { [key: string]: string } }
}

type Props = {
	pending: boolean,
	filters: IFiltersStore,
	reportService?: IReportStore<TFiltersData>,
	filtersData: {
		[key: string]: any
	}  | null,
	appliedLabelsMapping: TLabelsMapping,
	storedData: TSimpleFormData,
	configFactory: TSimpleFormConfigFactory,
	onApply: (d: any) => any
	onRemove?: () => void
	onClear?: () => void
}


const Filters = observer((
	{
		pending,
		filters,
		filtersData,
		appliedLabelsMapping,
		storedData,
		configFactory,
		onApply,
		reportService,
		onRemove = () => {}, // default func
		onClear = () => {}, // default func
	}: Props) => {
	const { t } = useTranslation();

	const applyHandler = (formData: TSimpleFormData) => {
		onApply(formData);
	};

	const clearHandler = () => {
		filters.reset();
		onClear();
	};

	const removeHandler = (key: string) => {
		filters.remove(key);
		onRemove();
	};

	const preventHandler = () => {};

	const appliedFiltersData = filtersData ? filtersData.filters : {};

	return (
		<div className="m-filters">
			<div className="m-filters__list">
				{
					filters.appliedExists && (
						<FiltersApplied
							pending={pending}
							onRemove={pending ? preventHandler : removeHandler}
							itemsData={appliedFiltersData}
							labelsMapping={appliedLabelsMapping}
							items={filters.applied}
						/>
					)

				}
			</div>
			<div className="m-filters__buttons">
				{
					filters.appliedExists && (
						<Button disabled={pending} type="primary-grey" icon="close" onClick={pending ? preventHandler : clearHandler }>Clear filters</Button>
					)
				}
				<Button disabled={pending} onClick={pending ? preventHandler : filters.showForm} icon="filter">{ filters.appliedExists ? t("Edit filters") : t("Filters") }</Button>

				{
					reportService && (
						<Button
							type="primary"
							disabled={pending}
							pending={reportService.pending}
							onClick={reportService.pending || pending ? preventHandler : reportService.requestAndSave}
							icon="export">{ t("Export to Excel") }</Button>
					)
				}
			</div>
			{
				filters.formShown && (
					<FiltersForm
						storedData={storedData}
						configFactory={configFactory}
						onCancel={filters.hideForm}
						onClose={filters.hideForm}
						onSubmit={applyHandler}
					/>
				)
			}

			{
				reportService && reportService.pending && (
					<PopupAlert title={t("Exporting...")} description={<PendingIcon/>} />
				)
			}

			{
				reportService && reportService.loaded && (
					<PopupAlert
						title={t("Loading was began automatically")}
						description={t("This message will closed in a few seconds")}
						onConfirm={() => reportService.setLoaded(false)}
					/>
				)
			}
		</div>
	);
});


export default Filters;
