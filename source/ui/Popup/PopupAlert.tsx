import React, {ReactNode} from "react";
import Popup from "./Popup";
import { Label, Paragraph } from "../Typography";
import Button from "../Button";


type Props = {
	title?: ReactNode,
	description?: ReactNode,
	confirmLabel?: string,
	onConfirm?: () => void
};

const PopupAlert = (
	{
		title,
		description,
		confirmLabel = "Ok",
		onConfirm
	}: Props) => {

	return (
		<Popup onClose={onConfirm} layer="1" size="alert">

			{ title && <Label className="c-popup__title">{title}</Label> }
			{ description && <Paragraph className="c-popup__description">{description}</Paragraph>  }

			{
				onConfirm && (
					<div className="c-popup__actions">
						<Button type="primary" onClick={onConfirm}>{confirmLabel}</Button>
					</div>
				)
			}
		</Popup>
	);
};

export default PopupAlert;
