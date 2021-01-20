import { formatDate, getPredefinedFormat } from "../formatDate";
import {TDateFormat} from "@app-types/TDateFormat";

describe("getPredefinedFormat", () => {
	const formats = {
		"datetime": "dd.MM.yyyy HH:mm"
	};

	it("get exists format key", () => {
		expect(getPredefinedFormat("datetime", formats)).toBe("dd.MM.yyyy HH:mm");
	});

	it("get not exists format key", () => {
		expect(getPredefinedFormat("dd.MM", formats)).toBe("dd.MM");
	});

	it("get exists key from default object", () => {
		expect(getPredefinedFormat("datetime")).toBe("dd.MM.yyyy HH:mm");
	});
});

describe("formatDate", () => {
	const dateString: Date = new Date("Wed Jan 08 2020 15:24:08 GMT+0200");
	const timeStamp: number = 1578489848500;

	const formatDateString = (key: TDateFormat) => formatDate({ date: dateString, formatKey: key });
	const formatTimeStamp = (key: TDateFormat)  => formatDate({ date: timeStamp, formatKey: key });

	it("format in `datetime` mode", () => {
		expect(formatDateString("datetime")).toBe("08.01.2020 15:24");
		expect(formatTimeStamp("datetime")).toBe("08.01.2020 15:24");
	});
	it("format in `timedate` mode", () => {
		expect(formatDateString("timedate")).toBe("15:24 08.01.2020");
		expect(formatTimeStamp("timedate")).toBe("15:24 08.01.2020");
	});

	it("format in `date` mode", () => {
		expect(formatDateString("date")).toBe("08.01.2020");
		expect(formatTimeStamp("date")).toBe("08.01.2020");
	});

	it("format in `time` mode", () => {
		expect(formatDateString("time")).toBe("15:24");
		expect(formatTimeStamp("time")).toBe("15:24");
	});
});
