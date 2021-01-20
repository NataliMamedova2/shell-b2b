import { isDate } from "../isDate";

describe("isDate", () => {

	it("validates good date string", () => {
		const testDate = new Date("2019-11-25T16:49:27+02:00").toString();
		expect(isDate(testDate)).toBe(true);
	});

	it("validates timestamp", () => {
		const testDate = Date.now();
		expect(isDate(testDate)).toBe(true);
	});

	it("validates empty string", () => {
		expect(isDate("")).toBe(false);
	});

	it("validates no standart timestamp number", () => {
		expect(isDate(111111)).toBe(true);
	});
});
