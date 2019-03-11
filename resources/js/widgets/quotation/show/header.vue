<template>

    <div>

        <!-- Quotation Menu -->
        <menuToggle class="float-right" :quotationId="localQuotation.id" :editMode="localEditMode" 
                    @toggleEditMode="$emit('toggleEditMode', $event)">
        </menuToggle>

        <!-- Send dropdown button -->
        <sendQuotationBtn :quotation="localQuotation" class="float-right mr-2"></sendQuotationBtn>

        <!-- Preview button -->
        <basicButton @click.native="downloadPDF({ preview: true })" size="small" class="float-right mr-2">Preview</basicButton>

    </div>

</template>
<script type="text/javascript">

    import basicButton from './../../../components/_common/buttons/basicButton.vue';
    import sendQuotationBtn from './../../../components/_common/buttons/sendQuotationBtn.vue';
    import menuToggle from './../../../components/_common/dropdowns/quotationMenuDropdown.vue';
   
    export default {
        props:{
            quotation: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        components: { basicButton, sendQuotationBtn, menuToggle },
        data(){
            return {
                localQuotation: this.quotation,
                localEditMode: this.editMode
            }
        },
        watch: {
            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {

                    //  Update the local quotation value
                    this.localQuotation = val;

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
