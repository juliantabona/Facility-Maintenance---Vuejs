<template>

        <!-- Dropdown button -->
        <Dropdown class="mr-4" trigger="click" placement="bottom-end">
            <!-- Send button - Dropdown trigger -->
            <Button type="primary" size="small">
                <span>Send</span>
                <Icon type="ios-send-outline" :size="20" style="margin-top: -4px;"/>
            </Button>
            <!-- Dropdown -->
            <DropdownMenu :style="{ width:'350px' }" slot="list">
                <!-- Send with email -->
                <DropdownItem @click.native="isOpenSendQuotationModal = true">Send With Email</DropdownItem>
                <!-- Share link -->
                <DropdownItem>
                    <p>Share Link</p>
                    <Input value="https://optimumqbw.com/quotation/GUYSD54983IIOWIW728UUIH2344IUH2I332D" style="width: 100%" :readonly="true" />
                </DropdownItem>
            </DropdownMenu>

            <!-- 
                MODAL TO SEND INVOICE - VIA EMAIL
            -->
            <sendQuotationModal 
                v-if="isOpenSendQuotationModal" 
                :quotation="localQuotation" 
                @visibility="isOpenSendQuotationModal = $event"
                @sent="updateParent($event)">
            </sendQuotationModal>
            
        </Dropdown>

</template>


<script type="text/javascript">

    import sendQuotationModal from './../modals/sendQuotationModal.vue';

    export default {
        components: { sendQuotationModal },
        props: {
            quotation: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                isOpenSendQuotationModal: false,
                localQuotation: this.quotation
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
            }
        },
        methods: {
            closeModal(){
                this.isOpenSendQuotationModal = !this.isOpenSendQuotationModal;
            },
            updateParent(updatedQuotation){
                //  Alert parent and pass updated quotation data
                this.$emit('sent', updatedQuotation);
            }
        }
    }

</script>
