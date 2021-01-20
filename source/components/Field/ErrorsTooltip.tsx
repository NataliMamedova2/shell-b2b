import React from "react";
import {Note} from "../../ui/Typography";
import Tooltip from "../../ui/Tooltip/Tooltip";
import uuid4 from "uuid/v4";
import {toJS} from "mobx";

type ErrorsProps = {
	errors: string[] | { [key: string]: string[] }
};

function extractErrors (acc: string[], errors: any): any[] {
	if(typeof errors === "string") {
		return  [...acc, errors];
	}

	if(Array.isArray(errors)) {
		return errors.map((item: any) => extractErrors(acc, item));
	}

	if(errors !== null && typeof errors === "object") {
		return Object.values(errors).map((item: any) => extractErrors(acc, item));
	}

	return acc;
}

const ErrorsContent = ({ errors }: ErrorsProps) => {

	if(!errors) {
		return null;
	}

	const messages: string[] = extractErrors([], errors).flat();

	return (
		<ul className="c-field__errors-list">
			{
				messages.map((message: string, index: number) => (
					<Note as="li" color="dark" key={index} className="c-field__errors-item">
						{  message }
					</Note>
				))
			}
		</ul>
	);
};

const ErrorsTooltip = ({errors}: ErrorsProps) => {

	if(errors.length === 0 ) {
		return null;
	}

	return (
		<Tooltip danger={true} message={<ErrorsContent errors={toJS(errors)} />} size="large" tooltipKey={uuid4()} />
	);
};

export default ErrorsTooltip;
