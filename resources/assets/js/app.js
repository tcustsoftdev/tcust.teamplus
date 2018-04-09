

import './bootstrap';

import vSelect from 'vue-select'
Vue.component('v-select', vSelect);

//packages
Vue.component('datetime-picker', require('./packages/components/DateTimePicker'));
Vue.component('alert', require('./packages/components/Alert'));
Vue.component('modal', require('./packages/components/Modal'));
Vue.component('toggle', require('./packages/components/Toggle'));
Vue.component('delete-confirm', require('./packages/components/DeleteConfirm'));
Vue.component('drop-down', require('./packages/components/DropDown'));
Vue.component('html-editor', require('./packages/components/HtmlEditor'));
Vue.component('check-box', require('./packages/components/Checkbox'));
Vue.component('check-box-list', require('./packages/components/CheckboxList'));


//global
Vue.component('page-controll', require('./components/page-controll'));
Vue.component('admin-card', require('./components/admin-card'));
Vue.component('searcher', require('./components/searcher'));


//views

Vue.component('notices-index', require('./views/notices/index'));


import Footer from './components/footer'
new Vue({
  render: h => h(Footer)
}).$mount('#footer')
