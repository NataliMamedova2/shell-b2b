import {useTranslation} from "react-i18next";
import React, {Fragment, useCallback} from "react";
import {getFullName} from "../../../libs";
import Button from "../../../ui/Button";
import {H4, Label, Paragraph} from "../../../ui/Typography";
import PendingIcon from "../../../ui/Icon/PendingIcon";

type Props = {
	pending: boolean,
	driver: any,
	onAction: () => void
	onRemove: () => void
}

const OwnerActions = React.memo(({driver, onAction, onRemove}: Omit<Props, "pending">) => {
	const { t } = useTranslation();

	return (
		<div className="m-cards-edit__owner-actions">
			<Button onClick={onAction}>
				{ driver ? t("Change owner") : t("Set owner") }
			</Button>

			{
				driver && (
					<Button type="alt" onClick={onRemove}>
						{  t("Remove owner") }
					</Button>
				)
			}
		</div>
	);
});

const CardOwner = ({ pending, driver, onAction, onRemove }: Props) => {
	const { t } = useTranslation();

	const renderFullName = useCallback(driver => getFullName(driver).long, [driver]);
	const renderCarsNumbers = useCallback(driver => driver.carsNumbers.map((item: any) => item.number).join(", "), [driver]);

	return (
		<div className="m-cards-edit__owner">
			<Label>{ t("Owner") }</Label>

			{
				(!pending && driver) && (
					<Fragment>
						<H4 className="m-cards-edit__name">{ renderFullName(driver) }</H4>
						<Paragraph>{ renderCarsNumbers(driver) }</Paragraph>
					</Fragment>
				)
			}

			{
				pending ? <PendingIcon/> : <OwnerActions driver={driver} onAction={onAction} onRemove={onRemove}/>
			}
		</div>
	);
};

export default CardOwner;
