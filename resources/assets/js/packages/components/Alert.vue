<template>
    <div  v-bind:class="{
                            'alert':		true,
                            'alert-success':(type == 'success'),
                            'alert-warning':(type == 'warning'),
                            'alert-info':	(type == 'info'),
                            'alert-danger':	(type == 'danger'),
                            'top': 			(placement === 'top'),
                            'top-right': 	(placement === 'top-right')
                         }"
    transition="fade"
    v-bind:style="{ width:width }"
    role="alert"> 
        <button v-show="dismissable" type="button" class="close" @click="close">
            <span>&times;</span>
        </button>
        <slot></slot>    
    </div>
</template>

<script>
    export default {
        props: {
            show: {
                type: Boolean,
                default: true
            },
            type: {
                type: String
            },
            dismissable: {
                type: Boolean,
                default: true
            },
            duration: {
                type: Number,
                default: 0
            },
            width: {
                type: String               
            },
            placement: {
                type: String
            }
        },

        methods: {
            close() {
                this.$emit('closed');
            },

        },
        watch: {
            show(show) {
                if (this._timeout) clearTimeout(this._timeout)
                if (this.show && Boolean(this.duration)) {
                    this._timeout = setTimeout(() => {
                        this.$emit('closed');
                    }, this.duration)
                }
            }
        }
    }
</script>


<style>
    .fade-transition {
        transition: opacity .3s ease;
    }
    
    .fade-enter,
    .fade-leave {
        height: 0;
        opacity: 0;
    }
    
    .alert.top {
        position: fixed;
        top: 30px;
        margin: 0 auto;
        left: 0;
        right: 0;
        z-index: 1050;
    }
    
    .alert {
        font-family: "微軟正黑體", "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 16px;
    }
    
    .alert.top-right {
        position: fixed;
        top: 30px;
        right: 50px;
        z-index: 1050;
    }
</style>