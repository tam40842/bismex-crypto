import Model from "./model";

export default class SupportModel extends Model {
	subject = null;
	message = null;

	constructor() {
		super();
	}
	json() {
		return {
			subject: this.subject,
			message: this.message,
		};
	}
	clear() {
		this.subject = null;
		this.message = null;
	}
}
