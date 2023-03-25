import _ from "lodash";
import moment from "moment";
export default class TradingModel {
  blur_60 = Array(60);
  blur_100 = Array(100);
  total_sell_60 = 0;
  total_buy_60 = 0;
  total_sell_100 = 0;
  total_buy_100 = 0;

  get get_blur_60() {
    return this.blur_60;
  }

  get get_blur_100() {
    return this.blur_100;
  }

  get get_total_sell60() {
    return this.total_sell_60;
  }

  get get_total_buy60() {
    return this.total_buy_60;
  }

  get get_total_sell100() {
    return this.total_sell_100;
  }

  get get_total_buy100() {
    return this.total_buy_100;
  }

  general(data) {
    if (data.length > 0) {

      let time = Number(moment().format("m"));
      let lastT = Number(moment().format("ss")) > 30 ? 2 : 1;
      let lastK = 0;
      if (time > 6) {
        lastK =
          lastT + 2 * (time - (7 + Math.abs(10 * Math.floor((time - 7) / 10))));
      } else {
        lastK = lastT + 2 * (time + 3);
      }

      let tempblur_60 = _.takeRight(data, 40 + lastK);
      let tempblur_100 = _.takeRight(data, 80 + lastK);
      // let tempblur_60 = _.takeRight(data, 60);
      // let tempblur_100 = _.takeRight(data, 100);
      let blur_60 = [];
      let blur_100 = [];

      this.total_sell_60 = tempblur_60.filter(item => item == 'CALL').length;
      this.total_buy_60 = tempblur_60.length - this.total_sell_60;
      this.total_sell_100 = tempblur_100.filter(item => item == 'CALL').length;
      this.total_buy_100 = tempblur_100.length - this.total_sell_100;

      if (tempblur_100.length < 100) {
        blur_100 = tempblur_100.concat(
          Array(100 - tempblur_100.length).fill("")
        );
      } else{
        blur_100 = tempblur_100;
      }

      if (tempblur_60.length < 60) {
        blur_60 = tempblur_60.concat(Array(60 - tempblur_60.length).fill(""));
      } else{
        blur_60 = tempblur_60;
      }

      this.blur_100 = _.chunk(_.chunk(blur_100, 4), 5);
      this.blur_60 = _.chunk(_.chunk(blur_60, 4), 5);
    }
  }
}
