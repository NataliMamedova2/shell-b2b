import React, { Component } from "react";
import "./styles.scss";
import Button from "../../ui/Button";
import {
	TSimpleFormData,
	TSimpleFormField,
	TSimpleFormError,
	TSimpleFormGroup, TSimpleFormState, TSimpleFormProps
} from "@app-types/TSimpleForm";
import {Form, FormRow, FormCol, FormGroups, FormGroup, FormActions, FormPlaceholder} from "../../ui/Form";
import {Prompt} from "react-router-dom";
import { withTranslation, WithTranslation } from "react-i18next";
import Text from "../../ui/Typography";
import Score from "../Score";
import Tooltip from "../../ui/Tooltip";
import ScrollToError from "../../ui/ScrollToError";
import isEqual from "lodash/isEqual";
import debounce from "lodash/debounce";
import Field from "../Field";
import uuid from "uuid/v4";
import {logger} from "../../libs";

export type Props = TSimpleFormProps & WithTranslation;

type State = {
	ready: boolean,
	edited: boolean,
	errors: TSimpleFormError,
	initialData: TSimpleFormData,
	scrollToZeroId: string
} & TSimpleFormState

class SimpleForm extends Component<Props>{
	state: State = {
		ready: false,
		edited: false,
		data: {},
		errors: {},
		initialData: {},
		scrollToZeroId: uuid(),
	};

	componentDidMount(): void {

		const { config, storedData } = this.props;

		const formConfig = typeof config === "function" ? config(this.props.t) : config;

		const fields: TSimpleFormField[] = formConfig.reduce((acc: any, current: TSimpleFormGroup): [] => {
			acc = [ ...acc, ...current.fields ];
			return acc;
		}, []);

		const initialData = fields.reduce((acc: TSimpleFormData, current: TSimpleFormField ): TSimpleFormData => {
			acc[current.key] = storedData && storedData[current.key]
				? storedData[current.key]
				: current.defaultValue;

			return acc;
		}, {});

		this.setState({
			data: initialData,
			initialData: initialData,
			ready: true
		}, () => {
			logger("SimpleForm's init with data:", this.state.data);
		});
	}
	render() {

		if(!this.state.ready) {
			return null;
		}

		const {
			t,
			errors: outsideErrors = {},
			disabled: submitDisabled = false,
			cancelLabel,
			submitLabel,
			onCancel,
			onClear,
			clearLabel,
			config,
			score,
			scoreLabel,
			listenEditing = true,
			scrollToErrorSelector = ".c-field.is-error"
		} = this.props;


		const { errors: insideErrors, data } = this.state;

		const errors: TSimpleFormError = { ...outsideErrors, ...insideErrors };
		const isInsideErrors = Object.keys(insideErrors).length > 0;
		const isOutsideErrors = Object.keys(outsideErrors).length > 0;
		const disableSubmitButton = submitDisabled || (listenEditing && (isInsideErrors || !this.state.edited));
		const formConfig = typeof config === "function" ? config(this.props.t) : config;

		return (
			<Form>
				<Prompt
					message={t("Are you sure you want to leave this page? The changes you made will be lost.")}
					when={this.state.edited && listenEditing && !submitDisabled} />

				{ isOutsideErrors && (
					<ScrollToError
						errors={outsideErrors}
						parentSelector=".c-form"
						errorSelector={scrollToErrorSelector}
						offset={150} />
				) }

				<FormGroups>

					{ this.props.before ? this.props.before : null }

				{
					formConfig.map((group: TSimpleFormGroup, index) => (
						<FormGroup key={"group_" + index}>
							{group.title && (
								<Text
									as="p"
									type={group.titleStyle || "label"}
									className="c-form__group-title"
									color="dark"
								>
									{group.title}
									{ group.titleInfo && <Tooltip size="medium" message={group.titleInfo} tooltipKey={"form-group" + index} /> }
								</Text>
							)}
							{
								group.grid.map(( row, rowIndex: number ) => {
									return (
										<FormRow key={rowIndex} colsCount={row.length}>
											{
												row.map((col, colIndex: number) => {
													const field = SimpleForm.getFieldByKey({config: group.fields, key: col});
													const itemIndex = "".concat(colIndex.toString(), rowIndex.toString());

													if(!field) return <FormPlaceholder key={itemIndex} />;

													return (
														<FormCol key={field.key}>
															<Field
																field={field}
																errors={errors[field.key]}
																value={data[field.key]}
																onChange={this.changeHandler(field.key)}
															/>
														</FormCol>
													);
												})
											}
										</FormRow>
									);
								})
							}
						</FormGroup>
					))
				}
				</FormGroups>

				<FormActions>
					<Button
						asButton={true}
						type="primary"
						pending={this.props.pending}
						disabled={disableSubmitButton}
						onClick={disableSubmitButton ? this.submitPlaceholder : this.submitHandler}
					>
						{submitLabel}
					</Button>


					{ onCancel && <Button asButton={true} type="alt" onClick={this.cancelHandler}>{cancelLabel}</Button> }
					{ onClear && <Button asButton={true} type="alt" onClick={this.clearHandler}>{clearLabel}</Button> }
					{ (score && scoreLabel) && <Score className="c-score--form" value={score(this.state.data)} label={scoreLabel} /> }
				</FormActions>
			</Form>
		);
	}

	clearHandler = () => {
		this.setState((state: State) => ({
			edited: false,
			data: { ...state.initialData}
		}));
	};

	cancelHandler = () => {
		if(this.props.onCancel) this.props.onCancel();
	};

	submitHandler = () => {
		this.props.onSubmit(this.state.data);
		this.setState({
			edited: false,
		});
	};

	submitPlaceholder = () => {};

	changeHandler = (key: string) => (value: any) => {
		this.setState((state: State) => ({
			data: { ...state.data, [key]: value }
		}), this.changeReflectHandler(key));
	};


	getFormChangedStatus = (state: State): boolean => {
		return !isEqual(state.initialData, state.data);
	};

	setFormChangedStatus = () => (
		this.setState((state: State) => ({ edited: this.getFormChangedStatus(state) }))
	);

	getInsideValidationStatus = (state: State) => (
		this.props.onValidate ? this.props.onValidate(state.data) : {}
	);

	setInsideValidationStatus = () => {
		this.setState((state: State) => ({ errors: this.getInsideValidationStatus(state) }));
	};

	changeReflectHandler = (key: string) => debounce(() => {
		this.setFormChangedStatus();

		if(this.props.onValidate) {
			this.setInsideValidationStatus();
		}

		if(this.props.onChange) {
			this.props.onChange(this.state.data, key);
		}
	}, 100);

	static getFieldByKey = ({ config, key }: { config: TSimpleFormField[], key: string  }): TSimpleFormField | null => {
		const fieldsByKey = config.filter(f => f.key === key);

		if(fieldsByKey.length > 0) {
			return fieldsByKey[0];
		}
		return null;
	};

}

export default withTranslation()(SimpleForm);
