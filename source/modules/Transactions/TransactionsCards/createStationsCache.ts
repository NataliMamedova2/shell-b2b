type TStationCache = {
	has(id: string): boolean,
	add(id: string | string[]): void,
	get(): any[],
	remove(id: string): void,
	clear(): void
};

function createStationsCache(): TStationCache {
	let selected: any[] = [];

	function has(id: string) {
		return selected.includes(id);
	}

	function add(id: string | string[]) {
		const ids = Array.isArray(id) ? id : [id];
		const filteredIds = ids.filter(i => !has(i));
		selected = [
			...selected,
			...filteredIds
		];
	}

	function get() {
		return selected;
	}

	function remove(id: string) {
		selected = [...selected.filter((itemId: string) => itemId !== id)];
	}

	function clear() {
		selected = [];
	}

	return  {
		has, add, get, remove, clear
	};
}

export { createStationsCache };
