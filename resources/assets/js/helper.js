
import CommonService from './services/common';
class Helper {
	static isEmptyObj(obj){
		for(var key in obj) {
			if(obj.hasOwnProperty(key)) return false;
		}
		return true;
	}
	static getErrorMsg(error){
		
		if(!error.response.data) return '';
		if(error.response.data.msg) return error.response.data.msg[0] ;
		if(error.response.data.errors) return error.response.data.errors.msg[0] ;
	}
	static getScrollBarWidth() {
		return CommonService.getScrollBarWidth();
	}
	static showEditor(id){
		Bus.$emit('show-editor',id);
	}
	static BusEmitError(error, msg) {
		
		console.log(error);
		if (!msg) msg = "系統無回應，請稍後再試";

		Bus.$emit('errors',msg);
	}
	static BusEmitOK(msg) {
		
		if (!msg) msg = "資料已存檔";
			Bus.$emit('okmsg', msg);
		}
	static tryParseInt(val) {
		if (!val) return 0;
		return parseInt(val);
	}
	static isTrue(val) {
		if (typeof val == 'number') {
			return val > 0;
		} else if (typeof val == 'string') {
			if (val.toLowerCase() == 'true') return true;
			if (val == '1') return true;
			return false;
		} else if (typeof val == 'boolean') {
			return val;
		}

		return false;
	}
	static buildQuery(url, searchParams) {
		url += '?';
		for (let field in searchParams) {

			let value = searchParams[field];
			url += field + '=' + value + '&';

		}
		return url.substr(0, url.length - 1);

	}

	static boolOptions(){
		return CommonService.boolOptions();
	}
	
	static activeLabel(active){
		
		return CommonService.activeLabel(active);
	}
	
	static activeOptions(){
		return CommonService.activeOptions();
	}
	static reviewedOptions() {
		return CommonService.reviewedOptions()
	}
	static reviewedLabel(reviewed) {
		return CommonService.reviewedLabel(reviewed)
	}

	static genderOptions(){
		return CommonService.genderOptions();
	}

	static formatMoney(money, wantInt){
		return CommonService.formatMoney(money, wantInt);
	}

	static numberOptions(min, max, desc) {

        return CommonService.numberOptions(min, max, desc)
	}
	static replaceAll(strVal, oldVal, newVal) {
        if (!strVal) return ''
        return strVal.replace(new RegExp(oldVal, 'g'), newVal)
    }
	
	
}

export default Helper;