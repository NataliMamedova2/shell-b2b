import {get} from "../../libs";
import {saveAs} from "file-saver";
import {decorate, action, observable} from "mobx";

export interface IReportStore<T = any> {
	endpoint: string,
	readonly getParams: () => any,
	readonly createName: (params: T) => string,
	readonly pending: boolean,
	readonly loaded: boolean,
	requestAndSave: () => void
	setLoaded: (val: boolean) => void
}


class ReportStore<T> implements IReportStore<T>{
	public pending: boolean = false;
	// FIXME why this params called `loaded`. In fact it define state when document is downloading
	public loaded: boolean = false;

	constructor(
		readonly endpoint: string,
		readonly getParams: () => T,
		readonly createName: (params: T) => string
	) {
	}

	setPending = (val: boolean) => this.pending = val;
	setLoaded = (val: boolean) => this.loaded = val;

	requestAndSave = async () => {
		this.setPending(true);
		try {
			const res = await get({
				endpoint: this.endpoint,
				params: this.getParams(),
				responseType: "blob"
			});
			this.setPending(false);
			this.setLoaded(true);

			const params = this.getParams();
			const filename = this.createName(params);
			saveAs(res.data, filename);

			setTimeout(() => {
				this.setLoaded(false);
			}, 4000);

		} catch (e) {}
	};
}

decorate(ReportStore, {
	pending: observable,
	loaded: observable,
	setPending: action,
	setLoaded: action
});

export default ReportStore;
