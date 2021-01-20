import React, {Component} from "react";
import "./styles.scss";
import PopupConfirm from "../../ui/Popup/PopupConfirm";
import { withTranslation, WithTranslation } from "react-i18next";

type Props = {
	message: string,
	callback: (bool: boolean) => void
} & WithTranslation

type State = {
	hidden: boolean
}
class RouterPrompt extends Component<Props> {
	state: State = {
		hidden: false
	};

	cancel = () => {
		this.props.callback(false);
		this.setState({ hidden: true });
	};

	confirm = () => {
		this.props.callback(true);
		this.setState({ hidden: true });
	};

	render() {

		if(this.state.hidden) {
			return null;
		}
		const { t } = this.props;
		return (
			<PopupConfirm
				confirmLabel={ t("Back to edit") }
				cancelLabel={ t("Dont save") }
				title={this.props.message || t("Unsaved information will be removed") }
				description={ t("You are leaving the unsaved form. Are you sure?") }
				onClose={this.cancel}
				onConfirm={this.cancel}
				onCancel={this.confirm} />
		);
	}
}


export default withTranslation()(RouterPrompt);
