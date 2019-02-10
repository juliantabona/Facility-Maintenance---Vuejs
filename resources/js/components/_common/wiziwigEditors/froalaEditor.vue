<style>

    .fr-wrapper > div:nth-child(1){
        display: none !important;
    }

</style>

<template>

    <froala :tag="'textarea'" :config="localConfig" v-model="model" @input="$emit('update:content', model)"></froala>

</template>

<script>

    import VueFroala from 'vue-froala-wysiwyg';

    export default {
        props: {
            config: {  
                type: Object,
                default: () => { 
                    return {
                        height: 150,
                        heightMax: 200,
                        toolbarButtons: ['paragraphFormat', 'bold', 'italic', 'underline', '|', 'fontFamily', 'fontSize', 'color', 'paragraphStyle', 'lineHeight', '|', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'insertLink', 'insertImage', 'insertTable', '|', 'emoticons', 'fontAwesome', '|', 'undo', 'redo'],
                        placeholder: this.placeholder,
                        events : {
                            'froalaEditor.contentChanged' : function(e, editor) {
                                //  console.log(editor.selection.get());
                            }
                        }
                    }
                }
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
                localConfig: this.config
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

        }
    }
</script>