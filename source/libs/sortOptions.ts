import React, { FC } from "react";

export type TSortConfig = TSortConfigItem[];

type TSortConfigItem = {
	value?: string,
	title?: string,
	id: string
}

type TIndexedObject = { [property: string]: string}

type TSortableHeadProps = {
	value?: string,
	key: string,
}

const createSortOptions = (config: TSortConfig) => {
	return config.reduce((acc: TIndexedObject, current: TSortConfigItem): TIndexedObject => {
		if(current.value) {
			acc = {
				...acc,
				[`${current.value}_asc`]: `${current.title}, за зростанням`,
				[`${current.value}_desc`]: `${current.title}, за спаданням`
			};
			return acc;
		}
		return acc;
	}, {});
};

const createSortableTableHead = (config: TSortConfig, element: FC<TSortableHeadProps>) => {
	return config.map((item) => {

		if(item.value && item.title) {
			return React.createElement(element, { value: item.value, key: item.id }, item.title);
		}

		if(item.title) {
			return React.createElement(element, { key: item.id }, item.title);
		}

		return React.createElement(element, { key: item.id }, null);
	});
};

export { createSortOptions, createSortableTableHead };
