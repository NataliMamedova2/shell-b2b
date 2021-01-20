import {computed, observable, decorate} from "mobx";
import ListStore from "../../stores/ListStore";
import StaffStore from "../../stores/StaffStore";
import {TAccessRole} from "@app-types/TAccessRole";
import {TItemStatus} from "@app-types/TItemStatus";

type TUserItem = {
	username: string
	email: string
	firstName: string
	lastName: string
	middleName?: string
	phone: string
	role: TAccessRole
	status: TItemStatus
	createdAt: Date
	lastLoggedAt: Date
}

class UsersStore {
	list = new ListStore<TUserItem>("/company/employees", {
			page: "1",
			status: "all",
			sort: "createdAt",
			order: "desc"
	});
	staff = new StaffStore({
		create: "/company/employees/create",
		read: "/company/employees",
		update: "/company/employees/update",
		delete: "/company/employees/delete",
		changeStatus: "/company/employees/change-status",
	});

	get blockedCount (): number {
		return this.list.metaInfo ? this.list.metaInfo.blockedCount : 0;
	}

	get activeCount(): number {
		return this.list.metaInfo ? this.list.metaInfo.activeCount : 0;
	}

	get allUserCount(): number {
		return this.blockedCount + this.activeCount;
	}
}

decorate(UsersStore, {
	list: observable,
	staff: observable,
	blockedCount: computed,
	activeCount: computed,
	allUserCount: computed,
});

const usersStore = new UsersStore();

export default usersStore;
