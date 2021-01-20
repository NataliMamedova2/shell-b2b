import {decorate, observable} from "mobx";
import FormStore from "../../stores/FormStore";

class FeedbackStore {
	form = new FormStore("/feedback")
}

decorate(FeedbackStore, {
	form: observable
});

const feedbackStore = new FeedbackStore();

export default feedbackStore;
