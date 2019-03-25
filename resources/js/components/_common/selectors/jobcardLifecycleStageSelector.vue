<template>
    
    <!-- Stage Selector -->
    <div>
        <Select v-model="(localSelectedStage || {}).name" :style="{ width:'100%' }" 
                placeholder="Select lifecycle stage" not-found-text="No stages found"
                @on-change="updateSelectedStage($event)">
            <Option 
                v-for="(item, index) in stages" 
                :value="item" 
                :label="item" 
                :key="index">
                {{ item }}
            </Option>
        </Select>

        <!-- 
            MODAL TO CHANGE MOBILE MONEY ACCOUNT - VIA EMAIL
        -->
        <updateLifecycleStageModal 
            v-if="isOpenUpdateLifecycleStageModal" 
            :selectedStage="localSelectedStage" 
            @visibility="isOpenUpdateLifecycleStageModal = $event"
            @updated="$emit('updated', $event)">
        </updateLifecycleStageModal>

    </div>
</template>

<script>

    /*  Modals  */
    import updateLifecycleStageModal from './../modals/updateLifecycleStageModal.vue';


    export default {
        props: {
            selectedStage: {
                type: Object,
                default: null
            }
        },
        components: { updateLifecycleStageModal },
        data(){
            return {
                localSelectedStage: this.selectedStage,
                stages: ['Deposit Paid', 'Job Started', 'Job Pending', 'Job Cancelled', 'Inspection', 'Contract', 'Closed'],
                isOpenUpdateLifecycleStageModal: false
            }
        },
        watch: {
            selectedStage: function (val) {
                this.localSelectedStage = val;
            }
        },
        methods: {
            updateSelectedStage(name){
                if( name == 'Deposit Paid' ){
                    this.localSelectedStage = this.getDepositPaidTemplate();
                }

                this.isOpenUpdateLifecycleStageModal = true
            },
            getDepositPaidTemplate(){
              
                var template = 
                        {
                            name: 'Deposit Paid',        
                            linked_invoice_id: null,
                            currency_type: null,
                            payment_amount: null,
                            payment_method: null,
                            full_payment: false
                    }

                return template;
            }
        }
    };
</script>