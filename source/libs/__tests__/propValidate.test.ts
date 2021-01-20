import { propValidate } from "../propValidate";

describe("propValidate", () => {

	it("return original value", () => {
		const result = propValidate(10, (val) => val > 1, "-");
		expect(result).toEqual(10);
	});

	it("return fallback value", () => {
		const result = propValidate(10, (val) => val > 10, "-");

		expect(result).toEqual("-");
	});

	it("return transformed origin value", () => {
		const result = propValidate(10, (val) => val > 1, "-", (val) => val + 1);
		expect(result).toEqual(11);
	});

	it("return transformed value", () => {
		const result = propValidate<number, string>(
			parseInt("12", 10),
			(val) => val > 1,
			"-",
			(val) => val + 1);

		expect(result).toEqual(13);
	});

});
