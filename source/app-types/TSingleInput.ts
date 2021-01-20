import {TFieldTypeExtended} from "@app-types/TFieldType";

export type TSingleInputOptions = {
	disabled?: boolean,
	placeholder?: string,
	inputType?: string,
	defaultLabel?: string,
	labelAs?: string,
	label?: string,
	maxLength?: number,
	selectOptions?: { [key: string]: any },
	radioOptions?: { [key: string]: any },
	arrayOptions?: {
		removeFrom?: number,
		removeIcon?: "trash" | "close",
		disableRemoveButtons?: (val: any) => boolean,
		buttonLabel: string | {
			single: string,
			plural: string
		},
		expandable?: boolean,
		expandValue?: boolean,
		type: TFieldTypeExtended,
		defaultValue?: any,
		maxLength?: number,
		maxCount?: number
	},
	orientation?: "vertical" | "horizontal",
	showErrorPreview?: boolean,
	errorPreview?: string,
	[key: string]: any
}

export type TSingleInput = {
	value: string,
	onChange: (value: any) => void,
	options: TSingleInputOptions,
	error?: boolean,
	errors?: any
}
