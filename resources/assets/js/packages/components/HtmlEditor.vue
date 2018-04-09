<template> 

  <textarea class='form-control' :name='name'></textarea>
                 
</template>
<script>

    export default {
        name: 'HtmlEditor',
        props: {
            model: {
                required: true,
            },
            version:{
                type: Number,
                default: 0
            },
            language: {
                type: String,
                required: false,
                default: "zh-TW"
            },
            height: {
                type: Number,
                required: false,
                default: 160
            },
            minHeight: {
                type: Number,
                required: false,
                default: 160
            },
            maxHeight: {
                type: Number,
                required: false,
                default: 800
            },
            name: {
                type: String,
                required: false,
                default: ""
            },
            toolbar: {
                type: Array,
                required: false,
                default: function() {
                    return [
                        ["font", ["bold", "italic", "underline", "clear"]],
                        ["fontsize", ["fontsize"]],
                        ["para", ["ul", "ol", "paragraph"]],
                        ["color", ["color"]],
                        ["insert", ["link", "hr"]]
                    ];
                }
            }
        },
        data() {
            return {
                text:'',
            }
        },
        watch: {
            version: function () {
                this.text = this.control.summernote("code");
                this.$emit('html-value',this.text)
            }
           
        },
        created: function() {
            this.text=this.model
            this.control = null;
        },
        mounted: function() {
            //  initialize the summernote
            if (this.minHeight > this.height) {
                this.minHeight = this.height;
            }
            if (this.maxHeight < this.height) {
                this.maxHeight = this.height;
            }
            var me = this;
            this.control = $(this.$el);
            this.control.summernote({
                toolbar: false,
                lang: this.language,
                height: this.height,
                minHeight: this.minHeight,
                maxHeight: this.maxHeight,
                toolbar: this.toolbar,
                callbacks: {
                    onInit: function() {
                        me.control.summernote("code", me.text);
                    },
                }
            })
        },
        methods:{
            getValue(){
                 this.text = this.control.summernote("code");
                 return this.text;
            }
        }
        
    }
</script>