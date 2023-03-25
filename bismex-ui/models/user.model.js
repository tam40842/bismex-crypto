import Model from "./model";

export default class UserModel extends Model {
    username = null;
    password = null;
    current_password = null;
    password_confirmation = null;
    ref_id = null;
    email = null;
    accept = false;
    msg = null;
    twofa_code = null;
    first_name = null;
    last_name = null;
    phone_number = null;
    country = null;
    passport = null;
    token = null;
    constructor() {
        super()
    }

    json() {
        return {
            username: this.username,
            password: this.password,
            current_password: this.current_password,
            password_confirmation: this.password_confirmation,
            ref_id: this.ref_id,
            email: this.email,
            twofa_code: this.twofa_code,
            first_name: this.first_name,
            last_name: this.last_name,
            phone_number: this.phone_number,
            country: this.country,
            passport: this.passport,
            accept: this.accept,
        }

    }
    clear() {
        this.username = null;
        this.password = null;
        this.current_password = null;
        this.password_confirmation = null;
        this.ref_id = null;
        this.email = null;
        this.twofa_code = null;
        this.first_name = null;
        this.last_name = null;
        this.phone_number = null;
        this.country = null;
        this.passport = null;
        this.accept = false;
    }
    get login() {
        if (this.email && this.password) return true;
        else false
    }
    get register() {
        if (this.username && this.password && this.password_confirmation && this.email && this.accept) return true;
        else false
    }
}