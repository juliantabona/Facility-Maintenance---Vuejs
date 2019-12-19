<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :isSaving="false" 
            :hideModal="hideModal"
            title="Mark As Paid"
            okText="" cancelText="Cancel"
            @on-ok="" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Activity Number Card -->
                <Row :gutter="20">

                    <Col :span="24" class="mb-4">

                        <h5 class="font-weight-bold mb-2">Manual Payment</h5>

                        <span>
                            If you received payment manually via cash on hand, bank deposit, bank transfer, cheque e.t.c then mark this order as fully paid or partially paid. 
                        </span>

                    </Col>

                    <Col :span="12">
                        <IconAndCounterCard  
                            type="success"
                            class="p-4 mb-2"
                            title="Full Payment" 
                            :showCheckMark="false"
                            icon="ios-cash-outline"
                            :style="{ width: '100%' }" 
                            :checkMarkVisibility="false" 
                            :titleStyle="{ paddingTop: '0px' }" 
                            @click.native="$emit('selected', 'full_payment')"
                            :iconStyle="{ fontSize: '65px', marginTop: '-5px', display: 'block', color: '#868686' }">
                        </IconAndCounterCard>
                    </Col>

                    <Col :span="12">
                        <IconAndCounterCard 
                            type="success"
                            class="p-4 mb-2"
                            title="Partial Payment" 
                            :showCheckMark="false"
                            :style="{ width: '100%' }" 
                            :checkMarkVisibility="false"
                            :titleStyle="{ paddingTop: '10px', }" 
                            @click.native="$emit('selected', 'partial_payment')"
                            imageSrc="/images/icons/divi-icon-set/SVG/individual%20icons/wallet.svg">
                        </IconAndCounterCard>
                    </Col>
                    
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Modal Structure  */
    import mainModal from './main.vue';

    export default {
        components: { IconAndCounterCard, mainModal  },
        data(){
            return{
                selectedPaymentType: null,
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