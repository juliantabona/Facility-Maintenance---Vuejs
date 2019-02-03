<template>

    <div>
        <!-- Preview button -->
        <previewInvoiceBtn></previewInvoiceBtn>

        <!-- Send dropdown button -->
        <sendInvoiceBtn :invoice="localInvoice"></sendInvoiceBtn>
        
        <!-- Invoice Menu -->
        <menuToggle style="" :invoiceId="localInvoice.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
    </div>

</template>
<script type="text/javascript">

    import previewInvoiceBtn from './../buttons/previewInvoiceBtn.vue';
    import sendInvoiceBtn from './../buttons/sendInvoiceBtn.vue';
    import menuToggle from './../menus/menuToggle.vue';
   
    export default {
        props:{
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        components: { previewInvoiceBtn, sendInvoiceBtn, menuToggle },
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
        }
    }

</script>
