import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import XHR from "i18next-xhr-backend";
import {getCurrentLanguage} from "./getCurrentLanguage";

const lang = getCurrentLanguage();

i18n
	.use(XHR)
	.use(initReactI18next)
	.init({
		lng: lang,
		fallbackLng: lang,
		debug: false,
		saveMissing: false,
		keySeparator: false,
		load: "languageOnly",
		interpolation: {
			escapeValue: false,
		},
		detection: {
			order: ["spaLagDetector"],
		},
		backend: {
			loadPath: process.env.NODE_ENV === "production" ? "/api/v1/translations/{{lng}}" : "/locales/uk.json"
		},
		react: {
			wait: true,
			bindI18n: "languageChanged loaded",
			bindStore: "added removed",
			nsMode: "default"

		}
	});

export default i18n;
