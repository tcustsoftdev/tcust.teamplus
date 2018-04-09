<template>
   <div>
		<check-box v-for="(item,index) in options" :key="index" :value="item.value" :text="item.text"
			:default="beenSelected(item.value)" :horizontal="horizontal"
			 @selected="onItemSelected" @unselected="onItemUnSelected">

		</check-box>
   </div>
</template>

<script>
export default {
	name: 'CheckboxList',
	props: {
		options:{
			type:Array,
			default:null
		},
		default_values:{
			type:Array,
			default:null
		},
		horizontal:{
			type:Boolean,
			default:false
		}
	},
	data(){
		return{
			selectedValues:[],
		}
	},
	beforeMount() {
		this.init();
	},
	methods: {
		init(){
			if(this.default_values){
				this.selectedValues = this.default_values.slice(0);
			}
		},
		beenSelected(value){
			if(!this.selectedValues.length) return false;
			return this.selectedValues.includes(value);
		},
		onItemSelected(value){
			if(this.beenSelected(value)) return;

				this.selectedValues.push(value);
				
				this.$emit('select-changed',this.selectedValues);
			
		},
		onItemUnSelected(value){
			let i = this.selectedValues.indexOf(value);
			if(i != -1) {
					this.selectedValues.splice(i, 1);
					this.$emit('select-changed',this.selectedValues);
			}
		}
	}
   
}
</script>

