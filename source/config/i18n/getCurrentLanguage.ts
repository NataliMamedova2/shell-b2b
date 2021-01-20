import {DEFAULT_LANGUAGE, LANGUAGES_LIST} from "../../environment";

const getCurrentLanguage = (): string => {
	const { pathname } = window.location;
	const langPart = pathname.replace(/^\//, "").split("/")[0];

	if(langPart && langPart.length === 2 && LANGUAGES_LIST.includes(langPart)) {
		return langPart;
	}
	return DEFAULT_LANGUAGE;
};

export { getCurrentLanguage };
