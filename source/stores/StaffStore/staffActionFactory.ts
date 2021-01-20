import {TStaffAction, TStaffActionState} from "./types";
import {logger, post} from "../../libs";
import {STAFF_STATE_ERROR, STAFF_STATE_SUCCESS} from "./config";

type StaffActionFactory = (config: {
	setActionPending: (val: boolean) => void
	setActionState: (type: TStaffAction, state: TStaffActionState) => void,
	clearAction: () => void
}) => (state: {
	action: TStaffAction,
	url: string,
	data?: any,
	onSuccess?: (data: any) => void,
	onFail?: (e: any) => void
}) => void

const staffActionFactory: StaffActionFactory = ({setActionPending, setActionState, clearAction}) => ({ url, data, action, onSuccess, onFail }) => {
	setActionPending(true);

	post({endpoint: url, data })
		.then((res) => {
			logger(`STAFF ACTION RESPONSE ${action}`,{ "__ACTION": action, ...res});
			setActionState(action, STAFF_STATE_SUCCESS);
			if(typeof onSuccess !== "undefined") {
				onSuccess(res.data);
			}
		})
		.catch((error) => {
			setActionState(action, STAFF_STATE_ERROR);
			if(onFail) onFail(error);
		})
		.finally(() => {
			clearAction();
			setActionPending(false);
		});
};

export default staffActionFactory;
