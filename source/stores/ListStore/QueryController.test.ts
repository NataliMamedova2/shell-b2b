import QueryController from "./QueryController";

describe("QueryController", () => {
	let sourceUrl: string;
	let paramsAsArrayKeys: string[];

	describe("getHashes", () => {

		beforeEach(() => {
			sourceUrl = "http://domain.com/uk/transactions/cards?sort=amount&order=asc&regions=1&regions=2";
		});

		it("returns array with hashes", () => {
			expect(QueryController.getHashes(sourceUrl)).toEqual([
				"sort=amount",
				"order=asc",
				"regions=1",
				"regions=2",
			]);
		});
	});

	describe("getParamsFromSearch", () => {

		it("returns array for property that is a array", () => {

			sourceUrl = "http://domain.com/uk/transactions/cards?sort=amount&order=asc&regions=1&regions=2&type=test&type=me";
			paramsAsArrayKeys = ["regions", "type"];

			expect(QueryController.getParamsFromSearch(sourceUrl, paramsAsArrayKeys)).toEqual({
				sort: "amount",
				order: "asc",
				regions: ["1", "2"],
				type: ["test", "me"]
			});
		});
	});
});
