// Import Image
import logo from "~/assets/images/global/logo.png";
import logo_mobile from "~/assets/images/global/logo_mobile.png";
import logo_mobile2 from "~/assets/images/global/logo_mobile2.png";
import avatar_default from "~/assets/images/global/avatar_default.png";
import usa from "~/assets/images/global/usa.png";
import btn_history from "~/assets/images/global/view_history.png";

// Import svg
import menu from "~/assets/images/svg/menu.svg";
import buy from "~/assets/images/svg/buy.svg";
import sell from "~/assets/images/svg/sell.svg";
import coin from "~/assets/images/svg/coin.svg";
import copy from "~/assets/images/svg/copy.svg";
import logout from "~/assets/images/svg/logout.svg";
import calendar from "~/assets/images/svg/calendar.svg";
import send from "~/assets/images/svg/send.svg";
import money_bag from "~/assets/images/svg/money_bag.svg";
import timer from "~/assets/images/svg/timer.svg";
import view_history from "~/assets/images/svg/view_history.svg";
import win from "~/assets/images/svg/win.svg";

// Import KYC
import front from "~/assets/images/kyc/front.svg";
import back from "~/assets/images/kyc/back.svg";
import selfie from "~/assets/images/kyc/selfie.svg";

// Import Landing
import s1_laptop from "~/assets/images/landing/s1_laptop.png";
import s2 from "~/assets/images/landing/s2.svg";
import s3 from "~/assets/images/landing/s3.svg";
import s4 from "~/assets/images/landing/s4.svg";
import s5_1 from "~/assets/images/landing/s5_1.png";
import s5_2 from "~/assets/images/landing/s5_2.png";
import s5_3 from "~/assets/images/landing/s5_3.png";
import s6_1 from "~/assets/images/landing/s6_1.svg";
import s6_2 from "~/assets/images/landing/s6_2.svg";
import s6_3 from "~/assets/images/landing/s6_3.svg";
import s6_bg_box from "~/assets/images/landing/s6_bg_box.svg";
class ImageAsset {
	image = {
		logo,
		logo_mobile,
		logo_mobile2,
		avatar_default,
		usa,
    btn_history
	};

	svg = {
		menu,
		buy,
		sell,
		coin,
		copy,
		logout,
		calendar,
		send,
		money_bag,
		timer,
		view_history,
		win,
	};

	kyc = {
		front,
		back,
		selfie,
	};

	landing = {
		s1_laptop,
		s2,
		s3,
		s4,
		s5_1,
		s5_2,
		s5_3,
		s6_1,
		s6_2,
		s6_3,
		s6_bg_box,
	};
}

export default ({ app }, inject) => {
	inject("image", new ImageAsset().image);
	app.$image = new ImageAsset().image;

	inject("svg", new ImageAsset().svg);
	app.$svg = new ImageAsset().svg;

	inject("kyc", new ImageAsset().kyc);
	app.$kyc = new ImageAsset().kyc;

	inject("landing", new ImageAsset().landing);
	app.$landing = new ImageAsset().landing;
};
