<template>
    <div role="dialog"  v-bind:class="{'modal':true,'fade':effect === 'fade', 'zoom':effect === 'zoom'}">
        <div v-bind:class="{'modal-dialog':true,'modal-lg':large,'modal-sm':small}" role="document" v-bind:style="{width: optionalWidth}"> 
            <div class="modal-content">
                <slot name="modal-header">    
                    <div class="modal-header">
                        <button type="button" class="close" @click="close"><span>&times;</span></button>
                        <h4 class="modal-title">
                            <slot name="title">
                            <h3> {{title}} </h3>
                            </slot>
                        </h4>
                    </div>                   
                </slot>                            
                
                <slot name="modal-body">
                    <div class="modal-body"></div>
                </slot> 
                <slot name="modal-footer">
                    <div class="modal-footer" v-if="showbtn">
                        <button type="button" class="btn btn-primary" @click="ok">{{ ok_text }}</button>
                        <button type="button" v-show="cancel_text" class="btn btn-default" @click="close">{{ cancel_text }}</button>
                    </div>
                </slot>                  
            </div>                           
        </div>   
    </div>
</template>

<script>
    export default {
        props: {
            showbtn: {
                type: Boolean,
                default: true
            },
            ok_text: {
                type: String,
                default: '確認送出'
            },
            cancel_text: {
                type: String,
                default: '取消'
            },
            title: {
                type: String,
                default: ''
            },
            show: {
                required: true,
                type: Boolean,
                twoWay: true
            },
            width: {
                default: null
            },
            effect: {
                type: String,
                default: null
            },
            backdrop: {
                type: Boolean,
                default: true
            },
            large: {
                type: Boolean,
                default: false
            },
            small: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            optionalWidth() {
                if (this.width === null) {
                    return null
                } else if (Number.isInteger(this.width)) {
                    return this.width + 'px'
                }
                return this.width
            }
        },
        watch: {
            show(val) {
                const el = this.$el
                const body = document.body
                const scrollBarWidth = Helper.getScrollBarWidth()
                if (val) {
                    $(el).find('.modal-content').focus()
                    el.style.display = 'block'
                    setTimeout(() => $(el).addClass('in'), 0)
                    $(body).addClass('modal-open')
                    if (scrollBarWidth !== 0) {
                        body.style.paddingRight = scrollBarWidth + 'px'
                    }
                    if (this.backdrop) {
                        $(el).on('click', e => {

                            if (e.target === el) this.close()
                        })
                    }
                } else {
                    body.style.paddingRight = null
                    $(body).removeClass('modal-open')
                    $(el).removeClass('in').on('transitionend', () => {
                        $(el).off('click transitionend')
                        el.style.display = 'none'
                    })
                }
            }
        },
        methods: {
            ok() {
                this.$emit('ok');
            },
            close() {
                this.$emit('closed');
            },
        }
    }

</script>

<style>
    .modal {
        transition: all 0.3s ease;
    }
    
    .modal.in {
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .modal.zoom .modal-dialog {
        -webkit-transform: scale(0.1);
        -moz-transform: scale(0.1);
        -ms-transform: scale(0.1);
        transform: scale(0.1);
        top: 300px;
        opacity: 0;
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        transition: all 0.3s;
    }
    
    .modal.zoom.in .modal-dialog {
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);
        -webkit-transform: translate3d(0, -300px, 0);
        transform: translate3d(0, -300px, 0);
        opacity: 1;
    }
    
    .modal-header-danger {
        color: #fff;
        padding: 9px 15px;
        border-bottom: 1px solid #eee;
        background-color: #d9534f;
        -webkit-border-top-left-radius: 5px;
        -webkit-border-top-right-radius: 5px;
        -moz-border-radius-topleft: 5px;
        -moz-border-radius-topright: 5px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
</style>