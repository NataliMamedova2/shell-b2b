import React from "react";
import SimpleForm from "../../SimpleForm";
import Popup from "../../../ui/Popup";
import {TSimpleFormConfigFactory, TSimpleFormData} from "@app-types/TSimpleForm";
import {useTranslation} from "react-i18next";
import { Label } from "../../../ui/Typography";
import "./styles.scss";
import Callout from "../../../ui/Callout";

type Props = {
	configFactory: TSimpleFormConfigFactory,
	onSubmit: (data: TSimpleFormData) => void,
	onCancel: () => void,
	onClose: () => void,
	storedData: TSimpleFormData
}

const FiltersForm = ({configFactory, storedData, onSubmit, onClose, onCancel}: Props) => {
	const { t } = useTranslation();
	const filtersFormInfo = t("Options are based on transactions");

	return (
		<Popup closableOverlay={false} onClose={onClose} size="filters" className="c-filters-form">
			<div className="c-filters-form__header">
				<Label>{ t("Filters") }</Label>
			</div>
			<SimpleForm
				before={<Callout type="simple">{ filtersFormInfo }</Callout>}
				storedData={storedData}
				listenEditing={false}
				config={configFactory}
				onCancel={onCancel}
				onSubmit={onSubmit}
				cancelLabel={ t("Cancel") }
				submitLabel={ t("Apply filters") }/>
		</Popup>
	);
};

export default React.memo(FiltersForm);
