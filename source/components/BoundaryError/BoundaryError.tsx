import React, {Component, ReactNode} from "react";
import "./styles.scss";
import { Caption, H4, Paragraph } from "../../ui/Typography";
import { captureException } from "../../vendors/exeptions";

type Props = {
	children?: ReactNode,
	moduleName?: string
}

type State = {
	hasError: boolean,
	errorInfo: string
}

class ErrorBoundary extends Component<Props, State> {

	state = {
		hasError: false,
		errorInfo: ""
	};

	componentDidCatch(error: Error) {
		this.setState({
			hasError: true,
			errorInfo: error.message
		});

		if(process.env.NODE_ENV === "production") {
			captureException(error);
		}
		console.error(error);
	}

	render() {

		if(this.state.hasError) {
			return (
				<div className="c-boundary-error">
					<H4> Oops. Something went wrong with this part of cabinet</H4>
					{ this.props.moduleName && <Paragraph>[{this.props.moduleName.toUpperCase()}]</Paragraph> }
					<Caption>We will fix this issue as soon as possible</Caption>
				</div>
			);
		}

		return this.props.children;
	}
}

export default ErrorBoundary;
