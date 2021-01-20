export type TRbacStaticRule = string;
export type TRbacDynamicRule = { [key: string]: (data: any) => boolean }

export type TRbacRulesConfig = {
	[key: string]: {
		static: TRbacStaticRule[],
		dynamic?: TRbacDynamicRule,
	}
}

const homeActions: TRbacStaticRule[] = [
	"home:main",
	"home:bonuses"
];

const companyActions: TRbacStaticRule[] = [
	"company:main",
	"company:edit",
	"company:loyalty",
	"company:sms"
];

const documentsActions: TRbacStaticRule[] = [
	"documents:list",
	"documents:bill",
	"documents:act",
];

const usersActions: TRbacStaticRule[] = [
	"users:list",
	"users:actions-history",
	"users:create",
	"users:edit",
	"users:delete",
	"users:change-status",
	"users:self",
];

const cardsActions: TRbacStaticRule[] = [
	"cards:list",
	"cards:create",
	"cards:limits",
	"cards:edit",
];

/* eslint @typescript-eslint/no-unused-vars: off */  // --> OFF
const driversActions: TRbacStaticRule[] = [
	"drivers:list",
	"drivers:create",
	"drivers:edit",
	"drivers:delete",
	"drivers:change-status",
];

const ticketsActions: TRbacStaticRule[] = [
	"tickets:list",
	"tickets:create",
	"tickets:edit"
];
const notificationsActions: TRbacStaticRule[] = [
	"notifications:list"
];
const transactionsActions: TRbacStaticRule[] = [
	"transactions:list"
];
const feedbackActions: TRbacStaticRule[] = [
	"feedback:main"
];

const actionsExcept = (rules: TRbacStaticRule[], exceptRules: TRbacStaticRule[]): TRbacStaticRule[] => {
	return rules.filter(rule => {
		return !exceptRules.includes(rule);
	});
};

const actionsOneOf = (rules: TRbacStaticRule[], oneOf: TRbacStaticRule[]): TRbacStaticRule[] => {
	return rules.filter(rule => {
		return oneOf.includes(rule);
	});
};


const rbacRules: TRbacRulesConfig = {
	admin: {
		static: [
			...homeActions,
			...companyActions,
			...documentsActions,
			...usersActions,
			...cardsActions,
			...driversActions,
			...ticketsActions,
			...notificationsActions,
			...transactionsActions,
			...feedbackActions
		],
	},
	manager: {
		static: [
			// ...actionsOneOf(companyActions, ["company:main"]),
			...actionsExcept(homeActions, ["home:bonuses"]),
			...driversActions,
			...actionsOneOf(usersActions, ["users:self"]),
			...cardsActions,
			...ticketsActions,
			...notificationsActions,
			...transactionsActions,
			...feedbackActions
		]
	},
	accountant: {
		static: [
			// ...actionsOneOf(companyActions, ["company:main"]),
			...actionsExcept(homeActions, ["home:bonuses"]),
			...driversActions,
			...documentsActions,
			...actionsOneOf(usersActions, ["users:self"]),
			...cardsActions,
			...ticketsActions,
			...notificationsActions,
			...transactionsActions,
			...feedbackActions
		],
	},
};


export default rbacRules;
