import i18n from "../config/i18n";

type Dictionary<T, U = any> = {
	[K in keyof T]: () => string
}

const dictionaryFactory = <T = any>(namespace: Dictionary<T>) => {
	return (key: keyof T) => {

		if(namespace[key]) {
			return namespace[key]();
		}
		console.warn(`"${key}" is not valid key for dictionary. Please make sure you have added all of needed keys`);

		return i18n.t(key as string);
	};
};

/**
 * Status of card
 */
const CARD_STATUS = {
	"blocked": () => i18n.t("Blocked"),
	"active": () => i18n.t("Active"),
};
export type TCardStatus = typeof CARD_STATUS;

const printCardStatus = dictionaryFactory<TCardStatus>(CARD_STATUS);
/**
 * Type of document
 */
export const DOCUMENT_TYPE = {
	"invoice": () => i18n.t("Invoice"),
	"act-checking": () => i18n.t("Act checking"),
	"appendix-petroleum-products": () => i18n.t("Appendix petroleum products"),
	"card-invoice": () => i18n.t("Card invoice"),
	"acceptance-transfer-act": () => i18n.t("Acceptance transfer act"),
};
export type TDocumentType = typeof DOCUMENT_TYPE
const printDocumentType = dictionaryFactory<TDocumentType>(DOCUMENT_TYPE);

/**
 * Status of document
 */
const DOCUMENT_STATUS = {
	"formed-automatically": () => i18n.t("Formed automatically"),
	"formed-by-request": () => i18n.t("Formed by request"),
};
export type TDocumentStatus = typeof DOCUMENT_STATUS
const printDocumentStatus = dictionaryFactory<TDocumentStatus>(DOCUMENT_STATUS);

/**
 * Status of driver
 */
const DRIVER_STATUS = {
	"active": () => i18n.t("Active driver"),
	"blocked": () => i18n.t("Blocked driver"),
	"on-moderation": () => i18n.t("On moderation")
};
export type TDriverStatus = typeof DRIVER_STATUS;

const printDriverStatus = dictionaryFactory<TDriverStatus>(DRIVER_STATUS);

/**
 * Status of User
 */
const USER_STATUS = {
	"active": () => i18n.t("Active"),
	"blocked": () => i18n.t("Blocked"),
};

type TUserStatus = typeof USER_STATUS;
const printUserStatus = dictionaryFactory<TUserStatus>(USER_STATUS);


/**
 * Role of user
 */
const USER_ROLE = {
	"admin": () => i18n.t("Admin"),
	"manager": () => i18n.t("Manager"),
	"accountant": () => i18n.t("Accountant"),
};

type TUserRole = typeof USER_ROLE;
const printUserRole = dictionaryFactory<TUserRole>(USER_ROLE);

/**
 * Status of Transaction
 */

const TRANSACTION_STATUS = {
	"write-off": () => i18n.t("Write off"),
	"return": () => i18n.t("Return"),
	"replenishment": () => i18n.t("Replenishment")
};
export type TTransactionStatus = typeof TRANSACTION_STATUS;
const printTransactionStatus = dictionaryFactory<TTransactionStatus>(TRANSACTION_STATUS);

/**
 * Type of transaction
 */
const TRANSACTION_TYPE = {
	"discounting": () => i18n.t("Discounting"),
	"payment-on-account": () => i18n.t("Return"),
	"refill": () => i18n.t("Refill"),
	"discount": () => i18n.t("Discount"),
	"write-off-cards": () => i18n.t("Write off cards"),
};
export type TTransactionType = typeof TRANSACTION_TYPE;
const printTransactionType = dictionaryFactory<TTransactionType>(TRANSACTION_TYPE);

/**
 * Short days
 */

const SHORT_DAY_OF_WEEK = {
	"mon": () => i18n.t("Mon"),
	"tue": () => i18n.t("Tue"),
	"wed": () => i18n.t("Wed"),
	"thu": () => i18n.t("Thu"),
	"fri": () => i18n.t("Fri"),
	"sat": () => i18n.t("Sat"),
	"sun": () => i18n.t("Sun"),
};

const LONG_DAY_OF_WEEK = {
	"mon": () => i18n.t("Monday"),
	"tue": () => i18n.t("Tuesday"),
	"wed": () => i18n.t("Wednesday"),
	"thu": () => i18n.t("Thursday"),
	"fri": () => i18n.t("Friday"),
	"sat": () => i18n.t("Saturday"),
	"sun": () => i18n.t("Sunday"),
};

type TDayOfWeekType = typeof SHORT_DAY_OF_WEEK;

const printShortDayOfWeek = dictionaryFactory<TDayOfWeekType>(SHORT_DAY_OF_WEEK);
const printLongDayOfWeek = dictionaryFactory<TDayOfWeekType>(LONG_DAY_OF_WEEK);


const SHORT_LANGUAGE = {
	"en": () => i18n.t("EN"),
	"uk": () => i18n.t("UK")
};
type TShortLang = typeof SHORT_LANGUAGE;
const printShortLanguage = dictionaryFactory<TShortLang>(SHORT_LANGUAGE);

export {
	printCardStatus,
	printDocumentStatus,
	printDocumentType,
	printDriverStatus,
	printUserStatus,
	printUserRole,
	printTransactionStatus,
	printTransactionType,
	printShortDayOfWeek,
	printLongDayOfWeek,
	printShortLanguage
};

