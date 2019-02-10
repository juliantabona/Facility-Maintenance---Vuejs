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
                <DropdownItem @click.native="isOpenSendInvoiceModal = true">Send With Email</DropdownItem>
                <!-- Share link -->
                <DropdownItem>
                    <p>Share Link</p>
                    <Input value="https://optimumqbw.com/invoice/GUYSD54983IIOWIW728UUIH2344IUH2I332D" style="width: 100%" :readonly="true" />
                </DropdownItem>
            </DropdownMenu>

            <!-- 
                MODAL TO SEND INVOICE - VIA EMAIL
            -->
            <sendInvoiceModal 
                v-if="isOpenSendInvoiceModal" 
                :invoice="localInvoice" 
                @visibility="isOpenSendInvoiceModal = $event"
                @sent="updateParent($event)">
            </sendInvoiceModal>
            
        </Dropdown>

</template>


<script type="text/javascript">

    import sendInvoiceModal from './../modals/sendInvoiceModal.vue';

    export default {
        components: { sendInvoiceModal },
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                isOpenSendInvoiceModal: false,
                localInvoice: this.invoice
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
            }
        },
        methods: {
            closeModal(){
                this.isOpenSendInvoiceModal = !this.isOpenSendInvoiceModal;
            },
            updateParent(updatedInvoice){
                //  Alert parent and pass updated invoice data
                this.$emit('sent', updatedInvoice);
            }
        }
    }

</script>
