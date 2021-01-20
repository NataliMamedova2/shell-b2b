import React, {Component, ReactNode} from "react";
import Flatpickr from "flatpickr";

const hooks: string[] = [
	"onChange",
	"onOpen",
	"onClose",
	"onMonthChange",
	"onYearChange",
	"onReady",
	"onValueUpdate",
	"onDayCreate"
];
const callbacks: string[] = [
	"onCreate",
	"onDestroy"
];

type THook = () => any | Array<() => any>;
type TCallback = () => any;

type Props = Partial<{
	defaultValue: string,
	options: { [key: string]: any },
	onChange: any,
	onOpen: THook,
	onClose: THook,
	onMonthChange: THook,
	onYearChange: THook,
	onReady: THook,
	onValueUpdate: THook,
	onDayCreate: THook,
	onCreate: TCallback,
	onDestroy: TCallback,
	value: string | number | Array<string | number> | { [key: string]: string | number }
	children: ReactNode,
	className: string,
	render: (d: any) => void,
	placeholder: any
}>


class DateTimePicker extends Component<Props> {
	// @ts-ignore
	flatpickr: null | FlatpickrFn = null;

	static defaultProps = {
		options: {}
	};
	
	componentDidUpdate(prevProps: Props) {
		if (this.props.hasOwnProperty("value") && this.props.value !== prevProps.value) {
			this.flatpickr.setDate(this.props.value, false);
		}
		
		const { options } = this.props;
		const prevOptions = prevProps.options;
		
		hooks.forEach(hook => {
			if (this.props.hasOwnProperty(hook)) {
				// @ts-ignore
				options[hook] = this.props[hook];
			}
			// Add prev ones too so we can compare against them later
			if (prevProps.hasOwnProperty(hook)) {
				// @ts-ignore
				prevOptions[hook] = prevProps[hook];
			}
		});
		
		const optionsKeys = Object.getOwnPropertyNames(options);
		for (let index = optionsKeys.length - 1; index >= 0; index--) {
			const key = optionsKeys[index];
			// @ts-ignore
			let value = options[key];

			// @ts-ignore
			if (value !== prevOptions[key]) {
				// Hook handlers must be set as an array
				if (hooks.indexOf(key) !== -1 && !Array.isArray(value)) {
					value = [value];
				}
				
				this.flatpickr.set(key, value);
			}
		}
		
	}

	componentDidMount() {
		const options = {
			onClose: () => {
				// @ts-ignore
				this.node.blur && this.node.blur();
			},
			...this.props.options
		};
		
		// Add prop hooks to options
		hooks.forEach(hook => {
			// @ts-ignore
			if (this.props[hook]) {
				// @ts-ignore
				options[hook] = this.props[hook];
			}
		});
		// @ts-ignore
		this.flatpickr = new Flatpickr(this.node, options);
		
		if (this.props.hasOwnProperty("value")) {
			this.flatpickr.setDate(this.props.value, false);
		}
		
		const { onCreate } = this.props;
		// @ts-ignore
		if (onCreate) onCreate(this.flatpickr);
	}
	
	componentWillUnmount() {
		const { onDestroy } = this.props;
		// @ts-ignore
		if (onDestroy) onDestroy(this.flatpickr);
		this.flatpickr.destroy();
	}
	
	render() {
		// eslint-disable-next-line no-unused-vars
		const { options, defaultValue, value, children, render, ...props } = this.props;
		// @ts-ignore
		const ref = (node) => {
			// @ts-ignore
			this.node = node;
		};
		
		// Don"t pass hooks and callbacks to dom node
		hooks.forEach(hook => {
			// @ts-ignore
			delete props[hook];
		});
		callbacks.forEach(callback => {
			// @ts-ignore
			delete props[callback];
		});

		// @ts-ignore
		if (render) return render({ ...props, defaultValue, value }, ref);
		// @ts-ignore
		return options.wrap
			? (
				<div {...props} ref={ref}>
					{ children }
				</div>
			)
			: (
				<input {...props} defaultValue={defaultValue} ref={ref} />
			);
	}
}

export default DateTimePicker;
