import { getFullName } from "../getFullName";

type TName = {firstName: string, lastName: string, middleName: string};

describe("getFullName", () => {

	const fullName: TName = { firstName: "Ivan", middleName: "Petrovych", lastName: "Duha" };
	const fullNameWithoutMiddle: TName = { firstName: "Ivan", middleName: "", lastName: "Duha" };

	it("should return object with an appropriate modes", () => {
		const result = getFullName(fullName);
		expect(Object.keys(result)).toEqual(["short", "long"]);
	});

	it("should render full name in long mode", () => {
		const result = getFullName(fullName);

		expect(result.long).toBe("Duha Ivan Petrovych");
	});

	it("should render initials in short mode", () => {
		const result = getFullName(fullName);

		expect(result.short).toBe("Duha IP");
	});
	it("should not render middleName in long mode", () => {
		const result = getFullName(fullNameWithoutMiddle);

		expect(result.long).toBe("Duha Ivan");
	});

	it("should not render middleName char in short mode", () => {
		const result = getFullName(fullNameWithoutMiddle);

		expect(result.short).toBe("Duha I");
	});
});
