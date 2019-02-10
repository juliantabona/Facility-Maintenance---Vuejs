<template>

    <div>
    
        <!-- Invoice Heading -->
        <h1 v-if="!localEditMode" class="text-dark text-right" style="font-size: 35px;">{{ localInvoice.heading || '___' }}</h1>
        <el-input v-if="localEditMode" placeholder="Invoice heading" v-model="localInvoice.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

    </div>

</template>


<script type="text/javascript">


    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                localInvoice: this.invoice,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

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
