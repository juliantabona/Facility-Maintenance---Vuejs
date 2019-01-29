<style>

    .fr-wrapper > div:nth-child(1){
        display: none !important;
    }

</style>

<template>

    <div id="app">
        <froala :tag="'textarea'" :config="config" v-model="model" @input="$emit('update:content', model)"></froala>
    </div>

</template>

<script>

    import VueFroala from 'vue-froala-wysiwyg';

    export default {
        props: {
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
                config: {
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
        }
    }
</script>