import React, {ReactNode} from "react";
import "./styles.scss";
import Popup from "./Popup";

type Props = {
	children?: ReactNode,
	wrapperClassName?: string
}

const PopupForm = ({wrapperClassName, children}: Props) => {
	return (
		<Popup layer="3" size="form" wrapperClassName={wrapperClassName} className="c-popup--align-start">
			{ children }
		</Popup>
	);
};

export default PopupForm;
