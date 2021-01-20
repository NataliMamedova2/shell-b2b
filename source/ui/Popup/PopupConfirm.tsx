import React, {ReactNode} from "react";
import Popup from "./Popup";
import { Label, Paragraph } from "../Typography";
import Button from "../Button";
import {useBreakpoint} from "../../libs/Breakpoint";

const defaultProps = {
	confirmLabel: "Ok",
	cancelLabel: "Cancel"
};

type Props = {
	onConfirm: () => void
	onCancel: () => void
	onClose?: () => void
	pending?: boolean,
	title?: ReactNode,
	description?: ReactNode,
} & typeof defaultProps;

const PopupConfirm = ({ title, description, confirmLabel, cancelLabel, onConfirm, onCancel, onClose, pending }: Props) => {
	const { state: { isMobile } } = useBreakpoint();

	return (
		<Popup disableScroll={!isMobile} onClose={ onClose || onCancel} layer="3" size="confirm">
			{ title && <Label className="c-popup__title">{title}</Label> }
			{ description && <Paragraph className="c-popup__description">{description}</Paragraph> }

			<div className="c-popup__actions">
				<Button type="alt" onClick={onCancel}>{cancelLabel}</Button>
				<Button type="primary" onClick={onConfirm} pending={pending}>{confirmLabel}</Button>
			</div>
		</Popup>
	);
};

PopupConfirm.defaultProps = defaultProps;

export default PopupConfirm;
