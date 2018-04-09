<template>
   <div @click="selectedChange" :style="getStyle()" class="checkbox checkbox-info">
		<input class="styled" type="checkbox" :checked="selected">
		
		<label>
			{{ text }}
		</label>
   </div>
</template>

<script>
export default {
	props: ['value', 'default','text','horizontal'],
	name: "Checkbox",
	data() {
		return {
			selected: false
		};
	},
	watch: {
    	'default':'init'
	},
	beforeMount() {
		this.init();
	},
	methods: {
		init() {
			if (Helper.isTrue(this.default)) this.selected = true;
			else this.selected = false;
		},
		getStyle(){
			if(this.horizontal)  'margin-top: 0px;margin-bottom: 0px;';
			return  'display:inline;margin-top: 0px;margin-bottom: 0px;';
		},
		selectedChange() {
			this.selected = !this.selected;
			if (this.selected) {
				this.$emit("selected", this.value);
			} else {
				this.$emit("unselected", this.value);
			}
		}
	}
}
</script>

<style scoped>
.checkbox {
  padding-left: 20px;
}
.checkbox label {
  display: inline-block;
  vertical-align: middle;
  position: relative;
  padding-left: 5px;
}
.checkbox label::before {
  content: "";
  display: inline-block;
  position: absolute;
  width: 17px;
  height: 17px;
  left: 0;
  margin-left: -20px;

  border: 1px solid #cccccc;
  border-radius: 3px;
  background-color: #fff;
  -webkit-transition: border 0.15s ease-in-out, color 0.15s ease-in-out;
  -o-transition: border 0.15s ease-in-out, color 0.15s ease-in-out;
  transition: border 0.15s ease-in-out, color 0.15s ease-in-out;
}
.checkbox label::after {
  display: inline-block;
  position: absolute;
  width: 16px;
  height: 16px;
  left: 0;
  top: 0;
  margin-left: -20px;
  padding-left: 3px;
  padding-top: 1px;
  font-size: 11px;
  color: #555555;
}
.checkbox input[type="checkbox"],
.checkbox input[type="radio"] {
  opacity: 0;
  z-index: 1;
}
.checkbox input[type="checkbox"]:focus + label::before,
.checkbox input[type="radio"]:focus + label::before {
  outline: thin dotted;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
.checkbox input[type="checkbox"]:checked + label::after,
.checkbox input[type="radio"]:checked + label::after {
  font-family: "FontAwesome";
  content: "\f00c";
}
.checkbox input[type="checkbox"]:disabled + label,
.checkbox input[type="radio"]:disabled + label {
  opacity: 0.65;
}
.checkbox input[type="checkbox"]:disabled + label::before,
.checkbox input[type="radio"]:disabled + label::before {
  background-color: #eeeeee;
  cursor: not-allowed;
}
.checkbox.checkbox-circle label::before {
  border-radius: 50%;
}
.checkbox.checkbox-inline {
  margin-top: 0;
}

.checkbox-info input[type="checkbox"]:checked + label::before,
.checkbox-info input[type="radio"]:checked + label::before {
  background-color: #5bc0de;
  border-color: #5bc0de;
}
.checkbox-info input[type="checkbox"]:checked + label::after,
.checkbox-info input[type="radio"]:checked + label::after {
  color: #fff;
}

input[type="checkbox"].styled:checked + label:after,
input[type="radio"].styled:checked + label:after {
  font-family: "FontAwesome";
  content: "\f00c";
}
input[type="checkbox"] .styled:checked + label::before,
input[type="radio"] .styled:checked + label::before {
  color: #fff;
}
input[type="checkbox"] .styled:checked + label::after,
input[type="radio"] .styled:checked + label::after {
  color: #fff;
}
</style>