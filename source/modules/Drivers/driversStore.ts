import { observable, decorate } from "mobx";
import ListStore from "../../stores/ListStore";
import StaffStore from "../../stores/StaffStore";
import {TItemStatus} from "@app-types/TItemStatus";

export type TDriverListItem = {
	id: string,
	carsNumbers: {number: string}[],
	phones: {number: string}[],
	status: TItemStatus,
	email: string,
	note: string
	firstName: string,
	lastName: string,
	middleName: string
};

class DriversStore {
	list = new ListStore<TDriverListItem>("/drivers", {
		page: "1",
		status: "all", // "active" | "blocked" | "moderation"
		sort: "fullName",
		order: "desc"
	});
	staff = new StaffStore({
		read: "/drivers",
		create: "/drivers/create",
		update: "/drivers/update",
		delete: "/drivers/delete",
		changeStatus: "/drivers/change-status",
	});
}

decorate(DriversStore, {
	list: observable,
	staff: observable
});

const driversStore = new DriversStore();

export default driversStore;
