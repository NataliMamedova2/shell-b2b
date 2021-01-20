import {ReactNode} from "react";

export type TFieldType =
	| "Input"
	| "InputNumber"
	| "Password"
	| "Select"
	| "Radio"
	| "Textarea"
	| "Phone"
	| "Checkbox"
	| "Date"
	| "Array"

export type TFieldTypeExtended = TFieldType | ReactNode
