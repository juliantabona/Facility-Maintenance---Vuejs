<template>

    <div>
        <!-- Invoice Menu -->
        <menuToggle class="float-right" :InvoiceId="localInvoice.id" :editMode="localEditMode" 
                    @toggleEditMode="$emit('toggleEditMode', $event)">
        </menuToggle>

        <!-- Send dropdown button -->
        <sendInvoiceBtn :Invoice="localInvoice" class="float-right mr-2"></sendInvoiceBtn>

        <!-- Preview button -->
        <basicButton @click.native="downloadPDF({ preview: true })" size="small" class="float-right mr-2">Preview</basicButton>

    </div>

</template>
<script type="text/javascript">

    import basicButton from './../../../components/_common/buttons/basicButton.vue';
    import sendInvoiceBtn from './../../../components/_common/buttons/sendInvoiceBtn.vue';
    import menuToggle from './../../../components/_common/dropdowns/invoiceMenuDropdown.vue';
   
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
        components: { basicButton, sendInvoiceBtn, menuToggle },
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
