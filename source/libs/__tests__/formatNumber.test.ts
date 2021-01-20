import { formatNumber } from "../formatNumber";

describe("formatNumber", () => {
	it("format int with default config", () => {
		expect(formatNumber(1000)).toBe("1 000.00");
	});

	it("format int with custom divider", () => {
		expect(formatNumber(1000, "_")).toBe("1_000.00");
	});

	it("format float with default config", () => {
		expect(formatNumber(1000.1)).toBe("1 000.10");
	});
});
