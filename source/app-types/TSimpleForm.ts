import {TFieldTypeExtended} from "@app-types/TFieldType";
import {TSingleInputOptions} from "@app-types/TSingleInput";
import {TTextType} from "../ui/Typography/Text";
import i18next from "i18next";
import {ReactNode} from "react";

export type TSimpleFormField = {
	type: TFieldTypeExtended,
	label: string,
	key: string,
	options: TSingleInputOptions,
	defaultValue: any,
}

export type TSimpleFormData = {
	[key: string]: any
}

export type TSimpleFormError = {
	[key: string]: string[]
}

export type TSimpleFormGrid = string[];

export type TSimpleFormGroup = {
	title?: string,
	titleStyle?: TTextType,
	titleInfo?: string,
	fields: TSimpleFormField[],
	grid: TSimpleFormGrid[]
}

export type TSimpleFormState = {
	data: TSimpleFormData
	sent?: boolean,
}

export type TSimpleFormDynamicValidator = ((data: TSimpleFormData) => TSimpleFormError) | ((data: TSimpleFormData) => {})

export type TSimpleForm = TSimpleFormGroup[]

export type TSimpleFormConfigFactory = (t: i18next.TFunction) => TSimpleForm

export type TSimpleFormProps = {
	config: TSimpleFormConfigFactory,
	storedData?: TSimpleFormData,
	onSubmit: (data: TSimpleFormData) => void,
	submitLabel: string,
	onCancel?: () => void,
	cancelLabel?: string,
	onClear?: boolean,
	clearLabel?: string,
	onValidate?: TSimpleFormDynamicValidator
	onChange?: (data: TSimpleFormData, key: string) => void,
	score?: (data: TSimpleFormData) => string | number,
	scoreLabel?: string,
	pending?: boolean,
	errors?: TSimpleFormError,
	listenEditing?: boolean,
	disabled?: boolean,
	scrollToErrorSelector?: string
	before?: ReactNode
}
