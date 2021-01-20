import * as Sentry from "@sentry/browser";
import {APP_VERSION} from "../environment";

type TCaptureErrorType = "/POST API" | "/GET API" | "COMMON EXCEPTIONS";

type TCaptureErrorInfo = {
	name: string,
	type: TCaptureErrorType,
	message: string,
	payload: { [key: string]: any } | any
	endpoint?: string,
	status?: number,
}

const captureError = (errorInfo: TCaptureErrorInfo) => {
	Sentry.captureException(JSON.stringify(errorInfo, null, 4));
};

const captureException = (error: any) => Sentry.captureException(error);

const captureMessage = (message: any) => {
	Sentry.captureMessage(message);
};

const initExceptionsVendor = () => {
	if(process.env.NODE_ENV === "production") {
		Sentry.init({
			dsn: "https://9f16068d0e3a4462b3cb0a57f2a9faf5@sentry.io/1836132",
			release: APP_VERSION.toString(10)
		});
	}

	return Promise.resolve(true);
};

export { captureError, captureMessage, captureException, initExceptionsVendor };
