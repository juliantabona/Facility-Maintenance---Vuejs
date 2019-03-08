<style>

    .fr-wrapper > div:nth-child(1){
        display: none !important;
    }

</style>

<template>

    <froala ref="textEditor" :tag="'textarea'" :config="localConfig" v-model="model" @input="$emit('update:content', model)"></froala>

</template>

<script>

    import VueFroala from 'vue-froala-wysiwyg';

    export default {
        props: {
            height: {
                type: Number,
                default: null
            },
            heightMax: {
                type: Number,
                default: null
            },
            config: {
                type: Object,
                default: null
            },
            content: {
                type: String,
            },
            placeholder: {
                type: String,
            }
        },
        data () {
            return {
                model: this.content,
                localConfig: null,
            }
        },
        watch: {

            //  Watch for changes on the invoice
            content: {
                handler: function (val, oldVal) {

                    //  Update the model value
                    this.model = val;

                }
            }

        },
        methods: {
            getConfigurations: function(){  
                var self = this;

                return {
                    height: this.height || 150,
                    heightMax: this.heightMax || 200,
                    toolbarButtons: ['paragraphFormat', 'bold', 'italic', 'underline', '|', 'fontFamily', 'fontSize', 'color', 'paragraphStyle', 'lineHeight', '|', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'insertLink', 'insertImage', 'insertTable', '|', 'emoticons', 'fontAwesome', '|', 'undo', 'redo'],
                    placeholder: this.placeholder,
                    events : {
                        'froalaEditor.contentChanged' : function(e, editor) {
                            //  console.log(editor.selection.get());
                        },
                        'froalaEditor.focus' : function(e, editor) {
                            self.$emit('onFocus', editor);
                        },
                        'froalaEditor.blur' : function(e, editor) {
                            self.$emit('onBlur', editor);
                        }
                    }
                }
            },
        },
        created(){
            
            if( this.config ){
                this.localConfig = this.config;

            }else{
                this.localConfig = this.getConfigurations();

            }
            
        }
    }
</script>