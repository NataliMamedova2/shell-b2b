import ItemController, {IItemController} from "./ItemController";

type TSourceValue = {
	name: string,
	age: number,
	married: boolean
}

const sourceId: string = "1-2-3-4-5";
const sourceValue: TSourceValue = {
	name: "John",
	age: 31,
	married: false
};

describe("ItemController", () => {
	let item: IItemController<TSourceValue>;

	beforeEach(() => {
		item = new ItemController(sourceId, sourceValue);
	});

	it("stores id and value through constructor", () => {
		expect(item.id).toBe(sourceId);
		expect(item.value).toEqual(sourceValue);
		expect(item.value.age).toBe(31);
	});

	it("updates value", () => {
		item.updateValue({ married: true });
		expect(item.value.married).toBe(true);
	});

	it("returns updated value", () => {
		const updatedItem = item.updateValue({ married: true, name: "Joe", });
		expect(updatedItem).toEqual({name: "Joe", age: 31, married: true });
	});
});
