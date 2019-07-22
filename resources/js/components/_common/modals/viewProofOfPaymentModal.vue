<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :isSaving="false" 
            :hideModal="hideModal"
            title="Proof Of Payment"
            okText="" cancelText="Cancel"
            @on-ok="" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <Row :gutter="20" class="pt-3 pb-3" style="background:#4abf7f;">

                    <Col :span="20" :offset="2">
                        <!-- Receipt Info -->
                        <span class="d-block text-white text-center mt-3 mb-3"><span class="font-weight-bold">Uploaded Date: </span> Jun 12 2019 @ 13:45</span>
                        
                        <!-- Receipts -->
                        <slick ref="slick" :options="slickOptions">
                            <div><img src="images/samples/proof_of_payment_3.png" width="100%" class="pl-2 pr-2"></div>
                            <div><img src="images/samples/proof_of_payment_1.png" width="100%" class="pl-2 pr-2"></div>
                            <div><img src="images/samples/proof_of_payment_2.png" width="100%" class="pl-2 pr-2"></div>
                        </slick>

                    </Col>
                    
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    import Slick from 'vue-slick';

    /*  Modal Structure  */
    import mainModal from './main.vue';

    export default {
        components: { Slick, mainModal  },
        data(){
            return{
                hideModal: false,
                //  Slick slider options
                slickOptions: {
                    dots: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                },
            }
        }
    }
</script>