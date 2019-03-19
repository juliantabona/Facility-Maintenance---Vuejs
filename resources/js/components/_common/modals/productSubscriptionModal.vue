<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :isSaving="false" 
            :hideModal="hideModal"
            title="Subscription Packages"
            okText="" cancelText="Cancel"
            @on-ok="" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Activity Number Card -->
                <Row :gutter="20">
                    <Col :span="8">
                        <subscriptionCard 
                            title="Get Sms Power" 
                            description="Get more sms's to send invoices, quotations, receipts and more..."
                            btnName="Get SMS's"
                            :ripple="false"
                            imgSrc="/images/assets/graphics/power-sms-subscription-solo.png" 
                            type="success"
                            :count="0" 
                            class="mb-2" 
                            :route="{ name: 'show-invoice-activities', params: { id: 1 } }">
                        </subscriptionCard>
                    </Col>

                    <Col :span="8">
                        <subscriptionCard 
                            title="Smart Reports" 
                            description="Subscribe to get daily, weekly or monthly reports via sms and email about your business"
                            btnName="Subscribe"
                            :ripple="true"
                            imgSrc="/images/assets/graphics/reports-subscription-solo.png" 
                            type="success"
                            :count="0" 
                            class="mb-2" 
                            :route="{ name: 'show-invoice-activities', params: { id: 1 } }">
                        </subscriptionCard>
                    </Col>
                    
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Cards  */
    import subscriptionCard from './../../../components/_common/cards/subscriptionCard.vue';

    /*  Modal Structure  */
    import mainModal from './main.vue';

    export default {
        components: { subscriptionCard, mainModal  },
        data(){
            return{
                hideModal: false,
            }
        },
        methods: {
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>