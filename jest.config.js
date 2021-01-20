module.exports = {
	"roots": [
		"<rootDir>/source"
	],
	"testMatch": [
		"**/__tests__/**/*.+(ts|tsx|js)",
		"**/?(*.)+(spec|test).+(ts|tsx|js)"
	],
	"transform": {
		"^.+\\.(ts|tsx)$": "ts-jest"
	},
	"moduleNameMapper": {
		"\\.(jpg|jpeg|png|gif|eot|otf|webp|svg|ttf|woff|woff2|mp4|webm|wav|mp3|m4a|aac|oga)$": "<rootDir>/source/__mocks__/fileMock.js",
		"\\.(css|less|scss)$": "<rootDir>/source/__mocks__/styleMock.js"
	},
};
