import Vue from "vue";
import moment from "moment";
export default ({ app, store }, inject) => {
	Vue.filter("toCurrency", function(
		value,
		currency = "USD",
		minimumFractionDigits = 0,
	) {
		const formatter = new Intl.NumberFormat("en-IN", {
			style: "currency",
			currency,
			minimumFractionDigits,
		});
		if (typeof value !== "number") {
			return formatter.format(0).split("US$")[1];
		}

		return formatter.format(value).split("US$")[1];
	});

	Vue.filter("displayCurrency", function(value, minimumFractionDigits = 0) {
		const formatter = new Intl.NumberFormat("en-US", {
			style: "currency",
			currency: "USD",
			minimumFractionDigits,
		});
		if (typeof value !== "number") {
			return formatter.format(0).split("$")[1];
		}

		return formatter.format(value.toFixed(minimumFractionDigits)).split("$")[1];
	});

	Vue.filter("displayDate", function(value, format = "DD/MM/YY HH:mm") {
		if (value === "" || !moment(value).isValid()) {
			return "";
		}
		return moment(value).format(format);
	});

	Vue.filter("formatDate", function(value, format = "DD/MM/YYYY") {
		if (value === "" || !moment(value).isValid()) {
			return "";
		}
		return moment(value).format(format);
	});
};
