type TName = {firstName: string, lastName: string, middleName: string};
type TResult = { short: string, long: string }

type TNameFactory = (item: any & TName) => TResult;


const upperFirstChar = (string: string): string => string[0];

const getNameLongMode = ({firstName, lastName, middleName}: TName): string => {
	const parts = [lastName, firstName, middleName].filter(Boolean);

	return parts.join(" ");
};

const getNameShortMode = ({firstName, lastName, middleName}: TName): string => {
	const initialsParts = [firstName, middleName].filter(Boolean).map(upperFirstChar).join("");

	return `${lastName} ${initialsParts}`;
};

const getFullName: TNameFactory = (item) => ({
	short: getNameShortMode(item),
	long: getNameLongMode(item)
});

export { getFullName };
