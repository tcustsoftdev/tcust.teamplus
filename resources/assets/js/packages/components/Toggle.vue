<template>
    <div class="btn-group">
        <button type="button" v-for="(item,index) in items" :key="index" @click="changeSelectVal(item.value)"  
           v-bind:class="[isActive(item.value) ? 'btn btn-info' :  'btn btn-default']" v-text="item.text">
        </button>
    </div>
</template>


<style>
    .btn {
        outline: none !important;
    }
</style>

<script>
    export default {
        props: ['items', 'default_val'],
        data() {
            return {
                selectedValue: 0,
            }
        },
        watch:{
            'default_val':'init'
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                this.selectedValue=this.default_val
            },
            changeSelectVal(val) {
                this.selectedValue = val
                this.$emit('selected', val);
            },
            isActive(key) {
                if(this.selectedValue ==null) this.selectedValue =this.defaultVal
                if (this.selectedValue == key) return true;
                return false
            },


        }




    }
</script>