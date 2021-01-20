import React, {Component, ReactNode} from "react";
import "./styles.scss";
import View from "../../../components/View";
import {FormLayout, FormLayoutAside, FormLayoutMain} from "../../../components/FormLayout";
import {H2, Label, Paragraph} from "../../../ui/Typography";
import Badge from "../../../ui/Badge";
import Button from "../../../ui/Button";
import PageHeader from "../../../components/PageHeader";
import CardsMessages from "../CardsMessages";
import cardsStore from "../cardsStore";
import {observer} from "mobx-react";
import { withTranslation, WithTranslation} from "react-i18next";
import { withRouter, RouteComponentProps } from "react-router-dom";
import SimpleForm from "../../../components/SimpleForm";
import {STAFF_ACTION_CHANGE_STATUS, STAFF_ACTION_UPDATE} from "../../../stores/StaffStore/config";
import {TSimpleFormData, TSimpleFormProps} from "@app-types/TSimpleForm";
import PendingIcon from "../../../ui/Icon/PendingIcon";
import createEditFormConfig from "./createEditFormConfig";
import Paper from "../../../ui/Paper";
import limitsStore, {customLimits} from "../limitsStore";
import { logger, post, propOf, toPrice} from "../../../libs";
import {printCardStatus} from "../../../config/dictionary";
import PageTitle from "../../../components/PageTitle";
import Search from "../../../components/Search";
import PopupForm from "../../../ui/Popup/PopupForm";
import DriverCreateForm from "../../Drivers/DriverCreate/DriverCreateForm";
import {TSearchOnSelectPayload} from "../../../components/Search/Search";
import CardOwner from "./CardOwner" ;
import PopupConfirm from "../../../ui/Popup/PopupConfirm";

type Props = {
	children?: ReactNode,
	id: string
} & RouteComponentProps & WithTranslation

type State = {
	cardData: TSimpleFormData,
	changeOwnerAction: TOwnerAction | null,
	changePending: boolean
}


const ACTION_SEARCH_USER = "SEARCH_OWNER";
const ACTION_CREATE_USER = "CREATE_NEW_OWNER";
const ACTION_DELETE_USER = "CREATE_DELETE_OWNER";
type TOwnerAction = typeof ACTION_CREATE_USER | typeof ACTION_SEARCH_USER | typeof ACTION_DELETE_USER;


class CardEdit extends Component<Props> {
	state: State = {
		cardData: {},
		changePending: false,
		changeOwnerAction: null
	};

	async componentDidMount() {
		await this.fetchCardData(false);
	}

	componentWillUnmount(): void {
		limitsStore.clearSelected();
	}

	render() {
		const { t, id } = this.props;
		const { fetchPending, isAction, actionPending, requestAction, errorPayload} = cardsStore.staff;
		const isDataReady = !fetchPending && Object.keys(this.state.cardData).length > 0;
		const {
			status = "...",
			cardNumber = "----------",
			onModeration = false
		}  = isDataReady ? this.state.cardData : { };

		const cardNumberForTitle = propOf<string>(this.state.cardData, "cardNumber", "", (num: any) => ` - #${num}`);

		return (
			<View className="m-cards-edit">
				<PageTitle contentString={`${t("Edit fuel card")}${cardNumberForTitle}`} />

				<PageHeader back="/cards">
					{
						(isDataReady && (status === "active") && !onModeration)
							&& <Button type="alt" onClick={() => requestAction(STAFF_ACTION_CHANGE_STATUS, id)}>{ t("Lock card") }</Button>
					}
					<Button type="alt" to={"/cards/limits/" + this.props.id}>{ t("Status limit of a card") }</Button>
				</PageHeader>
				<FormLayout>
					<FormLayoutAside>

						<div className="m-cards-edit__info">
							{
								onModeration
									&& <Paragraph className="m-cards-edit__status" color="error">• { t("change limit of card on moderation") }</Paragraph>
							}
							<Label>{ t("Fuel card") }</Label>
							<H2 className="m-cards-edit__number">{cardNumber}</H2>
							<Badge type="primary">{ printCardStatus(status) }</Badge>
							<CardOwner
								pending={!isDataReady || (isDataReady && (this.state.changePending || cardsStore.staff.silentPending))}
								driver={this.state.cardData.driver}
								onAction={this.requestChangeOwner(ACTION_SEARCH_USER)}
								onRemove={this.requestChangeOwner(ACTION_DELETE_USER)}
							/>
						</div>
					</FormLayoutAside>

					<FormLayoutMain>
						{
							(isDataReady)
								? (
									<SimpleForm
										config={createEditFormConfig}
										storedData={this.state.cardData}
										pending={ actionPending && isAction(STAFF_ACTION_UPDATE)}
										submitLabel={ t("Update card settings") }
										onSubmit={this.submitHandler}
										errors={errorPayload.validations}
										cancelLabel={t("Cancel")}
										onChange={this.changeHandler}
										onCancel={this.toCards}
										disabled={ onModeration }
									/>
								)
								: <Paper><PendingIcon/></Paper>
						}
					</FormLayoutMain>
				</FormLayout>

				<CardsMessages
					confirmChangeStatus={this.blockCardHandler}
				/>

				{
					this.state.changeOwnerAction === ACTION_CREATE_USER && (
						<PopupForm wrapperClassName="m-create-driver-popup">
							<DriverCreateForm
								onCancel={this.closeChangeOwner}
								onSubmit={this.applyChangeOwner}
								showCreatedMessage={false} />
						</PopupForm>
					)
				}

				{
					this.state.changeOwnerAction === ACTION_DELETE_USER && (
						<PopupConfirm
							title={t("The driver will be unassigned from this card. Are you sure?")}
							onCancel={this.closeChangeOwner}
							cancelLabel={ t("No")}
							confirmLabel={t("Yes")}
							pending={actionPending}
							onConfirm={this.applyDeleteOwner} />
					)
				}

				{
				this.state.changeOwnerAction === ACTION_CREATE_USER && (
						<PopupForm wrapperClassName="m-create-driver-popup">
							<DriverCreateForm
								onCancel={this.closeChangeOwner}
								onSubmit={this.applyChangeOwner}
								showCreatedMessage={false} />
						</PopupForm>
					)
				}

				{
					this.state.changeOwnerAction === ACTION_SEARCH_USER && (
						<Search
							title={ t("Drivers") }
							onCancel={this.closeChangeOwner}
							onSelect={this.applyChangeOwner}
							onEmpty={ this.requestChangeOwner(ACTION_CREATE_USER) }
							emptyActionLabel={t("Create driver")}
							endpoint="/drivers/search"
							extractKey="name"
							extractId="id"
							filterHandler={this.filterCurrentCardOwner}
						/>
					)
				}
			</View>
		);
	}

	requestChangeOwner = (action: TOwnerAction) => () => {
		this.setState({ changeOwnerAction: action });
	};
	closeChangeOwner = () => {
		this.setState({ changeOwnerAction: null });
	};

	applyChangeOwner = async (data: any) => {
		this.setState(() => ({ changePending: true }));
		this.closeChangeOwner();

		try {
			const res = await post({ endpoint: `/fuel-cards/change-driver/${this.props.id}`, data: { driverId: data.id } });

			this.setState((state: State) => ({
				cardData: {
					...state.cardData,
					driver: res.data
				}
			}));
		} catch (e) {

		}
		this.setState(() => ({ changePending: false }));
	};

	applyDeleteOwner = async () => {
		this.setState(() => ({ changePending: true }));
		this.closeChangeOwner();
		try {

			await post({ endpoint: `/fuel-cards/delete-driver/${this.props.id}`});
			await this.fetchCardData(true);

		} catch (e) { }

		this.setState(() => ({ changePending: false }));
	}

	toCards = () => {
		this.props.history.push("/cards");
	};

	blockCardHandler = () => {
		cardsStore.staff.changeStatus("blocked", () => {
			cardsStore.staff.clearAction();
			cardsStore.staff.clearActionState();
			this.toCards();
		});
	};

	changeHandler: TSimpleFormProps["onChange"] = (data, key) => {
		if( !customLimits.matches(key) ) {
			return;
		}
		const selectedData: string[] = limitsStore.toSelectedData(data[key]);
		limitsStore.setSelected(key as any, selectedData);
	};

	submitHandler = (data: TSimpleFormData) => {
		const preparedData = CardEdit.filterNameFromLimits(data);
		logger("SUBMIT UPDATE CARD", preparedData);
		cardsStore.staff.update(this.props.id, preparedData,this.updateCardData);
	};

	fetchCardData = async (silent: boolean) => {
		const data = await cardsStore.staff.read(this.props.id, silent);
		this.updateCardData(data);
	};

	filterCurrentCardOwner = (data: TSearchOnSelectPayload[]) => (
		data.filter((item) => item.id !== propOf(this.state.cardData, "driver.id", undefined))
	);

	updateCardData = (data: TSimpleFormData) => {
		logger("Card Data update", data);

		const preparedData = CardEdit.prepareCardDataBeforeSetToForm(data);

		this.setState((state: State) => ({
			cardData: {
				...state.cardData,
				...preparedData
			}
		}));
	};

	static prepareCardDataBeforeSetToForm = (data: TSimpleFormData): TSimpleFormData => {

		return Object.keys(data).reduce((acc: TSimpleFormData, currentKey: string) => {
			const currentValue = data[currentKey];

			if(customLimits.matches(currentKey)) {
				acc[currentKey] = currentValue.map((item: any) => ({
					...item,
					dayLimit: toPrice(item.dayLimit),
					monthLimit: toPrice(item.monthLimit),
					weekLimit: toPrice(item.weekLimit),
				}));
			} else if (currentKey === "totalLimits") {
				acc[currentKey] = {
					...currentValue,
					day: toPrice(currentValue.day),
					month: toPrice(currentValue.month),
					week: toPrice(currentValue.week),
				};
			} else {
				acc[currentKey] = currentValue;
			}
			return acc;
		}, {});
	};

	static filterNameFromLimits = (data: TSimpleFormData): TSimpleFormData => {
		return Object.keys(data).reduce((acc: TSimpleFormData, currentKey: string) => {
			const currentValue = data[currentKey];

			if(customLimits.matches(currentKey)) {
				acc[currentKey] = currentValue.map((item: any) => {
					const { name, ...rest } = item;
					return rest;
				});
			} else {
				acc[currentKey] = currentValue;
			}
			return acc;
		}, {});

	}
}

export default withTranslation()(withRouter(observer(CardEdit)));
