import React, { Fragment } from "react";
import {Label, Note} from "../../ui/Typography";
import "./styles.scss";
import {TFieldTypeExtended} from "@app-types/TFieldType";
import { TSingleInput } from "@app-types/TSingleInput";
import ErrorBoundary from "../../components/BoundaryError";
import classNames from "classnames";
import ErrorsTooltip from "./ErrorsTooltip";
import {getSingleInput} from "./helpers";

type Props = TSingleInput & {
	label: string,
	type: TFieldTypeExtended,
	errors?: string[]
}

const SingleField = ({label, type, errors, value, onChange, options }: Props) => {
	const SingleInput: any = getSingleInput(type);

	const fieldProps = {
		className: classNames("c-field", {
			"is-error": errors
		}),
	};

	const {
		labelAs = "label",
		errorPreview = "Invalid format",
		showErrorPreview = true
	} = options;

	return React.createElement(
		labelAs,
		fieldProps,
		(
			<Fragment>
				{ label && (
					<span className="c-field__label">
					{ errors && <ErrorsTooltip errors={errors}/> }
					<Label as="span">{label}</Label>
				</span>
				)}
				<ErrorBoundary moduleName={label}>
					<SingleInput
						errors={errors}
						error={errors && errors.length > 0}
						value={value}
						onChange={onChange}
						options={options} />
				</ErrorBoundary>
				{ (errors && showErrorPreview) && <Note className="c-field__error"> { errorPreview } </Note> }
			</Fragment>
		)
	);
};


export default SingleField;
