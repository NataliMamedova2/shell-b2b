import {ReactNode} from "react";
import {TFieldType} from "@app-types/TFieldType";

export type TFieldsMapping = { [T in TFieldType]: ReactNode | null }
