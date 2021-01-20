import {decorate, observable, computed, action} from "mobx";
import {TSimpleFormData} from "@app-types/TSimpleForm";
import axios from "axios";
import appAuthStore from "../../stores/AppAuthStore";
import {logger, get} from "../../libs";
import {TAccessRole} from "@app-types/TAccessRole";
import {ROLE_ADMIN} from "../../config/routes";

type TSimpleCallback = () => void;

class ProfileStore {
	pending: boolean = false;
	data: any = null;
	userRole: TAccessRole = "manager";

	readMe = (callback: TSimpleCallback) => {
		this.setPending(true);

		get({ endpoint: "/me" })
			.then(res => {
				this.setMeData(res.data);
				this.setUserRole(res.data.role);
				this.setPending(false);
				callback();
				logger("Profile data", res);
			})
			.catch(() => this.setPending(false));
	};

	updateMe = async (data: TSimpleFormData) => {
		await axios.post("/api/v1/me/update", data, {
			headers: {
				"Authorization": "Bearer " +  appAuthStore.token
			}
		});
	};

	setPending = (val: boolean) => this.pending = val;

	setMeData = (d:any) => {
		this.data = d;
	};

	setUserRole = (role: TAccessRole) => {
		this.userRole = role || ROLE_ADMIN;
	};

	updateCompanyName = (newName: string) => {

		if(!(this.data && this.data.company)) {
			return false;
		}

		this.data = {
			...this.data,
			company: {
				...this.data.company,
				name: newName || this.data.company.name
			}
		};

	};

	get myManager () {
		return this.data ? this.data.manager : {};
	}

	get myCompany () {
		return this.data ? this.data.company : {};
	}

	get me () {
		if(this.data) {
			const { company, manager, ...profile } = this.data;
			return profile;
		}
		return {};
	}

	get fullName () {
		const { firstName = "", lastName = "", middleName = "" } = this.me;

		return `${lastName} ${firstName}${middleName ? ` ${middleName}` : ""}`;
	}

}
decorate(ProfileStore,{
	pending: observable,
	data: observable,
	userRole: observable,
	setMeData: action,
	setPending: action,
	updateCompanyName: action,
	myCompany: computed,
	myManager: computed,
	me: computed,
	fullName: computed
});

const profileStore = new ProfileStore();

export default profileStore;
