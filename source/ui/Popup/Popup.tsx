import React, {Component, ReactNode, Fragment } from "react";
import ReactDOM from "react-dom";
import classNames from "classnames";
import "./styles.scss";
import Icon from "../Icon";
import ScrollDisable from "../ScrollDisable";
import FocusLock from "react-focus-lock";

type Props = {
	layer?: "0" | "1" | "2" | "3",
	className?: string,
	wrapperClassName?: string,
	children?: ReactNode,
	onClose?: () => void,
	onMount?: () => void,
	onDestroy?: () => void,
	disableScroll?: boolean,
	closableOverlay?: boolean
	size?: "alert" | "confirm" | "form" | "search" | "actions" | "filters"
}
type WrapperProps = Omit<Props, "disableScroll"> & {
	disableScroll: boolean
};

type PopupRoot = HTMLElement | null;

const PopupWrapper = ({ children, closableOverlay, disableScroll, className, size, onClose }: WrapperProps) => {

	const classes = classNames("c-popup__wrapper", {
		[className as string]: className,
		[`c-popup__wrapper--${size}`]: size
	});



	return (
		<Fragment>
			{ disableScroll && <ScrollDisable/> }
			<div className="c-popup__overlay" />
			<FocusLock
				className="c-popup__tab-box"
				as="div"
				// noFocusGuards={false}
				// @ts-ignore
				whiteList={node => document.getElementById("overlay-root") && document.getElementById("overlay-root").contains(node)}
			>
				<div className="c-popup__scroll-box" onClick={closableOverlay ? onClose : () => {}}>
					<div className={classes} onClick={e => e.stopPropagation()}>

						{ onClose && (
							<div className="c-popup__close" role="button" onClick={onClose}>
								<Icon tabIndex={0} type="close" />
							</div>
						)}

						<div className="c-popup__content">
							{ children }
						</div>
					</div>
				</div>
			</FocusLock>
		</Fragment>
	);
};

class Popup extends Component<Props> {
	contentNode: HTMLElement = document.createElement("div");
	root: PopupRoot = document.querySelector("#overlay-root");

	componentDidMount(): void {
		if (!this.root) {
			return;
		}
		const { className, layer, onMount } = this.props;
		this.contentNode.className = classNames("c-popup", `is-layer-${layer}`, { [className as string]: className });

		this.root.appendChild(this.contentNode);

		if(onMount) {
			onMount();
		}
	}

	componentWillUnmount(): void {
		if(!(this.root && this.contentNode)) {
			return;
		}
		if(this.contentNode) {
			this.root.removeChild(this.contentNode);
		}

		if(this.props.onDestroy) {
			this.props.onDestroy();
		}
	}

	render() {
		const disableScroll = typeof this.props.disableScroll !== "undefined" ? this.props.disableScroll : true;

		return ReactDOM.createPortal(
			<PopupWrapper closableOverlay={this.props.closableOverlay} disableScroll={disableScroll} onClose={this.props.onClose} className={this.props.wrapperClassName} size={this.props.size}>
				{ this.props.children }
			</PopupWrapper>,
			this.contentNode);
	}

	static defaultProps = {
		layer: 1,
		closableOverlay: false
	};
}

export default Popup;
