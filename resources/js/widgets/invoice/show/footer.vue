<style>

    footer {
        position: absolute; 
        bottom: 0; 
        left: 0; 
        right: 0;
        height: 40px;
        border-radius: 0 0 8px 8px;

        /** Extra personal styles **/
        font-size:12px;
        background-color: #dadada;
        color: white;
        text-align: center;
        line-height: 30px;
    }

</style>

<template>

    <footer :style="'background-color:'+primaryColor+';border-radius: 0 0 4px 4px;'">
        <div class="mt-1">
            <span v-if="!editMode">{{ localInvoice.footer }}</span>
            <el-input v-if="editMode" :placeholder="'e.g) Terms And Conditions Apply'" v-model="localInvoice.footer" size="mini" :style="{ width:'50%', margin:'0 auto' }"></el-input>
        </div>     
    </footer>

</template>


<script type="text/javascript">


    import froalaEditor from './../../../components/_common/wiziwigEditors/froalaEditor.vue';   

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
        },
        components: { froalaEditor },
        data(){
            return {
                localInvoice: this.invoice,
                localEditMode: this.editMode,
                primaryColor: (this.invoice.colors || {})[0],
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

                    //  Update the primary color shortcut
                    this.primaryColor = (val.colors || {})[0];


                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;

                }
            }

        },
    }

</script>
