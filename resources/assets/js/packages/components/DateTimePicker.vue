<template>
   <div class="date-input-group">
		         
        <flat-pickr v-model="selectedDate"  :config="config"  class="date-input"  >
		
		</flat-pickr>
      
        <div v-if="canClear" class="input-group-btn">
            <button  v-show="selectedDate" type="button" @click.prevent="clear" class="btn btn-default" title="Clear" data-clear>
                <i class="fa fa-times"> </i>            
            </button>
        </div>
   </div>
</template>

<script>

import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
	name:'DateTimePicker',
	props:{
		date:{
			type:String,
			default:''
        },
        can_clear:{
            type:Boolean,
			default:false
        }
	},
	components: {
      'flat-pickr':flatPickr
    },
	data(){
		return {
			config: {
				locale: this.datepickSettings()
			},
			selectedDate:null,
		}
    },
    computed:{
        canClear(){
            if(!this.can_clear) return false;
            if(!this.selectedDate) return false;
            return true;
        }
        
    },
	watch: {
        date(){
            this.init();
        },
		selectedDate(val){
			this.$emit('selected',val);
		},
	},
	beforeMount(){
		this.init();
	},
    methods:{
        init(){
            if(this.date) this.selectedDate=this.date;
		    else this.selectedDate=null;
        },
		clear(){
			this.selectedDate=null;
		},
        datepickSettings(){
            return {
                weekdays: {
                    shorthand: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
                    longhand: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"],
                },
                months: {
                    shorthand: [
                        "一月",
                        "二月",
                        "三月",
                        "四月",
                        "五月",
                        "六月",
                        "七月",
                        "八月",
                        "九月",
                        "十月",
                        "十一月",
                        "十二月",
                    ],
                    longhand: [
                        "一月",
                        "二月",
                        "三月",
                        "四月",
                        "五月",
                        "六月",
                        "七月",
                        "八月",
                        "九月",
                        "十月",
                        "十一月",
                        "十二月",
                    ],
                },
                rangeSeparator: " 至 ",
                weekAbbreviation: "周",
            
            }
        }
   }
}
</script>


<style scoped>
.date-input-group{
	 display: flex;
    align-items: center;
}
.date-input {
    width:85%;
	border: 1px solid #c3c3c3;
	height: 36px;
	line-height: 36px;
	
	padding: 8px;
	max-width: 360px;
	border-radius: 3px;
	background: #fff
}
</style>


