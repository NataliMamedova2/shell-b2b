import { action, observable, decorate } from "mobx";

export interface IItemController<T> {
	id: string,
	value: T,
	updateValue: (val: Partial<T>) => T
}

class ItemController<T> implements IItemController<T>{
	value: T;

	constructor(readonly id: string, value: T ) {
		this.value = value;
	}
	updateValue = (nextValue: Partial<T>) => {
		this.value = { ...this.value, ...nextValue };
		return this.value;
	};
}

decorate(ItemController, {
	value: observable,
	updateValue: action,
});

export default ItemController;
