class CommonService {
    static getScrollBarWidth() {
        if (document.documentElement.scrollHeight <= document.documentElement.clientHeight) {
            return 0;
        }
        let inner = document.createElement('p')
        inner.style.width = '100%';
        inner.style.height = '200px';

        let outer = document.createElement('div')
        outer.style.position = 'absolute';
        outer.style.top = '0px';
        outer.style.left = '0px';
        outer.style.visibility = 'hidden';
        outer.style.width = '200px';
        outer.style.height = '150px';
        outer.style.overflow = 'hidden';
        outer.appendChild(inner);

        document.body.appendChild(outer);
        let w1 = inner.offsetWidth;
        outer.style.overflow = 'scroll';
        let w2 = inner.offsetWidth;
        if (w1 === w2) w2 = outer.clientWidth;

        document.body.removeChild(outer);

        return (w1 - w2);
    }
    static activeText(active) {
        if (Helper.isTrue(active)) return '上架中';
        return '已下架';
    }
    static activeLabel(active) {
        let style = 'label label-default';
        if (Helper.isTrue(active))  style = 'label label-info';
        let text = this.activeText(active);

        return `<span class="${style}" > ${text} </span>`;
    }

    static reviewedText(reviewed) {
        if (Helper.isTrue(reviewed)) return '已審核';
        return '未審核';
    }
    static reviewedLabel(reviewed) {
        let style = 'label label-danger';
        if (Helper.isTrue(reviewed)) style = 'label label-success';
        let text = this.reviewedText(reviewed);
        return `<span class="${style}" > ${text} </span>`;

    }
    static reviewedOptions() {
        return [{
            text: '已審核',
            value: true
        }, {
            text: '未審核',
            value: false
        }]
    }
    static boolOptions() {
        return [{
            text: '是',
            value: true
        }, {
            text: '否',
            value: false
        }]
    }
    static activeOptions() {
        
        return [{
            text: '上架中',
            value: true
        }, {
            text: '已下架',
            value: false
        }];
    }
    static genderOptions() {
        return [{
            text: '先生',
            value: true
        }, {
            text: '小姐',
            value: false
        }]
    }

    static numberOptions(min, max, desc) {
        let options = []
        if (desc) {
            for (var i = max; i >= min; i--) {
                let option = {
                    text: i,
                    value: i
                }
                options.push(option)
            }
        } else {
            for (var i = min; i <= max; i++) {
                let option = {
                    text: i,
                    value: i
                }
                options.push(option)
            }
        }


        return options
    }
    static categoriesText(categories) {
        if (!categories.length) return ''
        let html = ''
        for (var i = 0; i < categories.length; i++) {
            html += categories[i].name + '&nbsp;'
        }
        return html
    }
        static namesText(names) {
            if (!names.length) return ''
            let html = ''
            for (var i = 0; i < names.length; i++) {
                html += names[i] + '&nbsp;'
            }
            return html
        }
    static formatMoney(money, wantInt) {
        if (!money) {
            if (wantInt)  return 0;
            return '';
        }
        money = String(money);
        let pos = money.indexOf(".");
        if (pos < 0) return money;

        let cents = parseInt(money.substring(pos + 1));
        if (cents > 0) return money;

        let text= money.substring(0, pos);
        if(wantInt && !text) return 0;
        return text;
    }




}


export default CommonService;