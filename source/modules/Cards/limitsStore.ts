import { decorate, action, observable } from "mobx";
import {TSimpleFormData} from "@app-types/TSimpleForm";

type Selected = {
	fuelLimits: string[],
	goodsLimits: string[],
	servicesLimits: string[],
}

export const customLimits: any = {
	items: Object.freeze({
		fuelLimits: {
			key: "fuelLimits",
			defaultValue: []
		},
		goodsLimits: {
			key: "goodsLimits",
			defaultValue: []
		},
		servicesLimits: {
			key: "servicesLimits",
			defaultValue: []
		},
	}),
	flatKeys(): string[]  {
		const selfItems = this.items;
		return Object.keys(selfItems).map((key: any) => selfItems[key as string].key);
	},
	mapKeys(): { [K in keyof Selected]: string } {
		const selfItems = this.items;
		return Object.keys(selfItems).reduce((acc: any, current: string) => {
			acc[current] = selfItems[current].key;
			return acc;
		}, {});
	},
	matches(key: string) {
		return this.flatKeys().includes(key);
	},
	initialState() {
		const selfItems = this.items;
		return Object.keys(selfItems).reduce((acc: any, currentKey: string) => {
			const currentItem = selfItems[currentKey];
			acc[currentItem.key] = currentItem.defaultValue;
			return acc;
		}, {});
	}
};


class LimitsStore {
	selected: Selected = {
		...customLimits.initialState()
	};

	clearSelected = () => {
		this.selected = {
			...customLimits.initialState()
		};
	};

	setSelected = (key: keyof Selected, value: string[]) => {
		this.selected[key] = value;
	};

	hasSelected = (key: keyof Selected, id: string): boolean => {
		return this.selected[key].includes(id);
	};

	toSelectedData = (data: TSimpleFormData) => {
		return data.map((item: any) => item.id);
	}
}

decorate(LimitsStore, {
	selected: observable,
	clearSelected: action,
	setSelected: action
});

const limitsStore = new LimitsStore();

export default limitsStore;
