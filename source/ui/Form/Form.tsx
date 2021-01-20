import React, {ReactNode} from "react";
import classNames from "classnames";
import "./styles.scss";

type Props = {
	children: ReactNode
}

const Form = ({children}: Props) => <div className="c-form"> {children}</div>;

const FormRow = ({children, colsCount}: Props & { colsCount: number }) => {
	const classes = classNames("c-form__row", `has-cols-${colsCount}`);
	return <div className={classes}> {children}</div>;
};

const FormCol = ({ children }: Props) => <div className="c-form__col">{children}</div>;
const FormSection = ({ children }: Props) => <div className="c-form__section">{children}</div>;
const FormActions = ({ children }: Props) => <div className="c-form__actions">{children}</div>;
const FormGroup = ({ children }: Props) => <div className="c-form__group">{children}</div>;
const FormGroups = ({ children }: Props) => <div className="c-form__groups">{children}</div>;
const FormPlaceholder = () => <div />;

export { Form, FormRow, FormCol, FormActions, FormSection, FormPlaceholder, FormGroups, FormGroup};
