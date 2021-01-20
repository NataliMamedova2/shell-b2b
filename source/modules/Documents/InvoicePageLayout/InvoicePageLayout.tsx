import React, {ReactNode} from "react";
import "./styles.scss";
import {
	FormLayout,
	FormLayoutAside,
	FormLayoutBack,
	FormLayoutInfo,
	FormLayoutMain
} from "../../../components/FormLayout";
import View from "../../../components/View";
import {useTranslation} from "react-i18next";

type Props = {
	children: ReactNode,
	to: string,
	className?: string
}

const InvoicePageLayout = ({children, to, className}: Props) => {
	const { t } = useTranslation();
	return (
		<View className={`m-bill-create ${className ? className : ""}`}>
			<FormLayoutBack to={to} />
			<FormLayout>
				<FormLayoutAside>
					<FormLayoutInfo
						icon="doc"
						title={t("Account Formation Application")}
						text={ t("Account Formation Application (description)") } />
				</FormLayoutAside>
				<FormLayoutMain>
					{ children }
				</FormLayoutMain>
			</FormLayout>
		</View>
	);
};

export default InvoicePageLayout;
