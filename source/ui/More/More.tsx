import React, {useState} from "react";
import "./styles.scss";
import Icon from "../Icon";
import classNames from "classnames";
import {TIconType} from "@app-types/TIconType";
import Popover from "../Popover";
import Action from "../Action";
import {useBreakpoint} from "../../libs/Breakpoint";
import Popup from "../Popup";

export type TMoreAction = {
	icon: TIconType,
	title: string,
	handler: () => void
} | null

type Props = {
	actions: TMoreAction[]
}

type ListProps = {
	actions: TMoreAction[],
	onActionClick?: () => void
}

const ActionsList = ({ actions, onActionClick }: ListProps) => {
	const clickHandler = (actionHandler: () => void) => () => {
		actionHandler();
		if(onActionClick) {
			onActionClick();
		}
	};

	return (
		<div className="c-more__actions">
			{ actions.map((action, index) => {
				return action
					? <Action key={index} icon={action.icon} title={action.title} onClick={clickHandler(action.handler)}/>
					: null;
			}) }
		</div>
	);
};

const MobileMore = ({ actions }: Props ) => {
	const [active, setActive] = useState(false);
	const classes = classNames("c-more", {
		"is-current-active": active
	});

	return (
		<div className="c-more-wrapper">
			<div className={classes} onClick={() => setActive(true)}>
				<Icon type="more" />
			</div>
			{
				active && (
					<Popup disableScroll={false} onClose={() => setActive(false)} size="actions" className="c-popup--more-actions">
						<ActionsList actions={actions} onActionClick={() => setActive(false)}/>
					</Popup>
				)
			}

		</div>
	);
};


const More = ({ actions}: Props) => {
	const { state: { acrossMobileTablet } } = useBreakpoint();

	if(acrossMobileTablet) {
		return <MobileMore actions={actions} />;
	}

	return (
		<Popover content={<ActionsList actions={actions}/>} anchor="bottom-left" className="c-more-wrapper">
			<div className="c-more">
				<Icon type="more" />
			</div>
		</Popover>
	);
};


const MorePlaceholder = () => (
	<span className="c-more-wrapper">
		<span className="c-more c-more--disabled">
			<Icon type="more" />
		</span>
	</span>
);

export { MorePlaceholder };
export default React.memo(More);
