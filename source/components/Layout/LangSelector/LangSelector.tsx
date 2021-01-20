import React, {SyntheticEvent, Component} from "react";
import { Note } from "../../../ui/Typography";
import "./styles.scss";
import { withTranslation, WithTranslation} from "react-i18next";
import { withRouter, RouteComponentProps } from "react-router-dom";
import { LANGUAGES_LIST } from "../../../environment";
import {printShortLanguage} from "../../../config/dictionary";

type Props = {} & RouteComponentProps & WithTranslation;

type State = {
	active: boolean
}

class LangSelector extends Component<Props> {
	state: State = {
		active: false
	};

	componentWillUnmount(): void {
		window.removeEventListener("click", this.hideList);
	}

	render() {
		const { location: { pathname }, i18n } = this.props;
		const { active } = this.state;

		const currentLanguage = LANGUAGES_LIST.filter(locale => locale === i18n.language)[0];
		const restLanguages = LANGUAGES_LIST.filter(locale => locale !== i18n.language);

		return (
			<div className="c-lang-selector">
	    <span role="button" className="c-lang-selector__item" onClick={this.toggleList}>
		    <Note>{ printShortLanguage(currentLanguage as any) }</Note>
	    </span>
				{
					active && restLanguages.length > 0 && (
						<ul className="c-lang-selector__list">
							{
								restLanguages.map((locale, index) => (
									<a href={ "/" + locale + pathname }  key={index} className="c-lang-selector__item">
										<Note>{printShortLanguage(locale as any)}</Note>
									</a>
								))
							}
						</ul>
					)
				}
			</div>
		);
	}

	stopPropagation = (e: SyntheticEvent) => e.stopPropagation();

	hideList = () => {
		this.setState({ active: false });
	};

	toggleList = (e: SyntheticEvent) => {
		this.stopPropagation(e);
		this.setState((state: State) => ({ active: !state.active }), () => {
			window.addEventListener("click", this.hideList);
			if(!this.state.active) {
				return window.removeEventListener("click", this.hideList);
			}
		});
	}
}

export default withTranslation()(withRouter(LangSelector));
