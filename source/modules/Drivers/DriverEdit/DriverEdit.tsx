import React, {Component} from "react";
import "./styles.scss";
import {FormLayout, FormLayoutAside, FormLayoutInfoWrapper, FormLayoutMain} from "../../../components/FormLayout";
import View from "../../../components/View";
import {observer} from "mobx-react";
import driversStore from "../driversStore";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import SimpleForm from "../../../components/SimpleForm";
import createDriverEditForm  from "../config";
import Paper from "../../../ui/Paper";
import PageHeader from "../../../components/PageHeader";
import Button from "../../../ui/Button";
import {withTranslation, WithTranslation} from "react-i18next";
import {RouteComponentProps, withRouter} from "react-router-dom";
import {
	STAFF_ACTION_CHANGE_STATUS,
	STAFF_ACTION_DELETE,
	STAFF_ACTION_UPDATE
} from "../../../stores/StaffStore/config";
import {Label, H4, Paragraph} from "../../../ui/Typography";
import Badge from "../../../ui/Badge";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import DriversMessages from "../DriversMessages/DriversMessages";
import { preparingDriverDataForSubmit } from "../prepareDriverDataForSubmit";
import {printDriverStatus} from "../../../config/dictionary";

type Props =  {
	id: string
} & WithTranslation & RouteComponentProps

type State = {
	driverData: TSimpleFormData & {
		firstName?: string,
		middleName?: string,
		lastName?: string
	}
}

class DriverEdit extends Component<Props> {
	state: State = {
		driverData: {}
	};

	async componentDidMount() {
		const data = await driversStore.staff.read(this.props.id);
		this.setState({ driverData: data });
	}

	componentWillUnmount(): void {
		driversStore.staff.resetStaffFormId();
	}

	render () {
		const { t } = this.props;
		const { driverData } = this.state;
		const { staffFormId, fetchPending, actionPending, isAction, errorPayload } = driversStore.staff;

		const fullName = Object.keys(driverData).length > 0 ? driversStore.staff.getFullName(driverData) : { short: "--", long: "--" };

		return (
			<View className="m-edit-driver">

				<PageHeader title={t("Edit driver")} back="/drivers">
					{
						!fetchPending
							? <Button type="alt" disabled={actionPending} onClick={this.requestDeleteAction} >{ t("Delete driver") }</Button>
							: null
					}
					<Button disabled={actionPending} type="alt" onClick={this.requestChangeStatusAction}>
						{ fetchPending ? <PendingIcon/> : driverData.status === "active" ? t("Block driver") : t("Unblock driver") }
					</Button>
				</PageHeader>
				<FormLayout>
					<FormLayoutAside>
						<FormLayoutInfoWrapper>
							<Label>{ t("Driver profile") }</Label>
							<H4>{fetchPending ? <PendingIcon/> : fullName.long}</H4>
							<Badge>{ fetchPending ? <PendingIcon/> : printDriverStatus(driverData.status) }</Badge>
							<Paragraph className="m-edit-driver__description">
								{ t("Driver profile [description]") }
							</Paragraph>
						</FormLayoutInfoWrapper>
					</FormLayoutAside>
					<FormLayoutMain>
						{
							Object.keys(driverData).length > 0 && !fetchPending
								? <SimpleForm
									key={staffFormId}
									storedData={driverData}
									errors={errorPayload.validations}
									onSubmit={this.updateHandler}
									pending={ actionPending && isAction(STAFF_ACTION_UPDATE) }
									submitLabel={ t("Update") }
									config={createDriverEditForm}/>
								: <Paper><PendingIcon/></Paper>
						}
					</FormLayoutMain>
				</FormLayout>

				<DriversMessages
					key="drivers_list_messages"
					afterChangeStatus={this.afterStatusChanged}
					afterDelete={this.afterUserDeleted}
				/>
			</View>
		);
	}

	requestChangeStatusAction = () => {
		driversStore.staff.requestAction(STAFF_ACTION_CHANGE_STATUS, this.props.id, { status: this.state.driverData.status });
	};
	requestDeleteAction = () => {
		driversStore.staff.requestAction(STAFF_ACTION_DELETE, this.props.id);
	};

	afterUserDeleted = () => {
		driversStore.staff.clearActionState();
		this.props.history.push("/drivers");
	};

	afterStatusChanged = async () => {
		driversStore.staff.clearActionState();
		const data = await driversStore.staff.read(this.props.id);
		this.setState({ driverData: data });
		driversStore.staff.resetStaffFormId();
	};

	updateHandler = (data: TSimpleFormData) => {
		driversStore.staff.update(this.props.id, preparingDriverDataForSubmit(data), (updatedData) => {
			this.setState(() => ({ driverData: updatedData }));
			driversStore.staff.resetStaffFormId();
		});
	};
}

export default withTranslation()(withRouter(observer(DriverEdit)));
