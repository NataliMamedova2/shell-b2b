import React, {Component} from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import Button from "../../ui/Button";
import Icon from "../../ui/Icon";
import {Label} from "../../ui/Typography";
import classNames from "classnames";
import {getButtonLabel, getSingleInput} from "./helpers";
import ErrorsTooltip from "./ErrorsTooltip";

type Props = Omit<TSingleInput, "value"> & {
	label: string,
	errors?: string[],
	value: any[]
}

class ArrayField extends Component<Props> {

	render() {
		if (!this.props.options.arrayOptions) return null;

		const {
			label,
			value = [],
			errors,
			options: {
				arrayOptions: {
					type,
					buttonLabel,
					removeIcon = "trash",
					removeFrom = 0,
					disableRemoveButtons,
					maxCount
				},
				...inputOptions
			}
		} = this.props;

		const SingleInput: any = getSingleInput(type);

		const addButtonLabel: string = getButtonLabel(buttonLabel, value.length);

		const fieldClasses = classNames("c-field c-field--array", {
			"is-error": errors
		});

		const filteredValue = Array.isArray(value) ? value.filter(item => item !== null) : [];
		const showAddButton = maxCount ?  maxCount > filteredValue.length : true;
		const canRemoveItem = (index: number): boolean => {
			const validIndex = index >= removeFrom;
			const disableButtons = disableRemoveButtons ? disableRemoveButtons(filteredValue) : false;

			return !disableButtons && validIndex;
		};

		return (
			<div className={fieldClasses}>
				{ label && (
					<span className="c-field__label">
						{ errors && <ErrorsTooltip errors={errors}/>}
						<Label as="span">{label}</Label>
					</span>
				)}

				<div className="c-field__array">
					{
						filteredValue.map((item: string, index: number) => {
							return (
								<div className="c-field__array-item" key={index}>
									<SingleInput
										errors={errors ? errors[index] : {}}
										error={errors && errors.length > 0}
										value={item}
										onChange={this.changeHandler(index)}
										options={inputOptions} />
									{
										canRemoveItem(index) && (
											<Icon type={removeIcon} onClick={ this.removeHandler(index) } />
										)
									}
								</div>
							);
						})
					}
				</div>

				{
					showAddButton && <Button type="ghost" onClick={this.addHandler}> + {addButtonLabel}</Button>
				}
			</div>
		);
	}

	removeHandler = (index: number) => () => {
		const newValue = [
			...this.props.value.slice(0, index),
			...this.props.value.slice(index + 1)
		];

		this.props.onChange(newValue);
	};

	changeHandler = (index: number) => (value: string) => {
		const newValue = [
			...this.props.value.slice(0, index),
			value,
			...this.props.value.slice(index + 1)
		].filter(item => item !== null);
		this.props.onChange(newValue);
	};

	addHandler = () => {
		const defaultValue = this.props.options.arrayOptions ? this.props.options.arrayOptions.defaultValue : "";
		const newValue = [
			...this.props.value,
			defaultValue
		];
		this.props.onChange(newValue);
	}
}


export default React.memo(ArrayField);
