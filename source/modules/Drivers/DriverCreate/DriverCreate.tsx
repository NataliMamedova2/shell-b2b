import React, { ReactNode } from "react";
import "./styles.scss";
import View from "../../../components/View";
import {
	FormLayoutBack
} from "../../../components/FormLayout";
import DriverCreateForm from "./DriverCreateForm";
import { useHistory } from "react-router-dom";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import {logger} from "../../../libs";

type Props =  {
	children?: ReactNode,
}

const DriverCreate = ({children, ...props}: Props) => {

	const history = useHistory();
	const toDrivers = () => history.push("/drivers");
	const showData = (d: TSimpleFormData) => logger("showData of driver", { d });

	return (
		<View className="m-create-driver-inline">
			<FormLayoutBack to="/drivers"/>
			<DriverCreateForm showCreatedMessage={true} onSubmit={showData} onConfirm={toDrivers} />
		</View>
	);
};

export default DriverCreate;
