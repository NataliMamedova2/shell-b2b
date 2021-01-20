import { useEffect } from "react";
import {useTranslation} from "react-i18next";

type Props = {
	contentString?: string
}
const PageTitle = ({ contentString }: Props) => {
	const { t } = useTranslation();
	const defaultString = t("Shell Cards");
	const resultString = contentString ? `${defaultString} | ${contentString}` : defaultString;

	useEffect(() => {
		document.title = resultString;

		return () => {
			document.title = defaultString;
		};
	}, [contentString, defaultString, resultString]);

	return null;
};


export default PageTitle;
