<style scoped>

    * {
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    font-size: 12px;
    }

    #breadcrumb {
    list-style: none;
    display: inline-block;
    }
    #breadcrumb >>> .icon {
    font-size: 14px;
    }
    #breadcrumb >>> li {
    float: left;
    margin-bottom:5px;
    cursor: pointer;
    }
    #breadcrumb >>> li span {
    color: #FFF;
    display: block;
    background: #3498db;
    text-decoration: none;
    position: relative;
    height: 40px;
    line-height: 40px;
    padding: 0 10px 0 5px;
    text-align: center;
    margin-right: 23px;
    }
    /*
    #breadcrumb >>> li:nth-child(even) span {
    background-color: #19be6b;
    }
    #breadcrumb >>> li:nth-child(even) span:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li:nth-child(even) span:after {
    border-left-color: #19be6b;
    }
    */
    #breadcrumb >>> li.active span {
    background-color: #19be6b;
    }
    #breadcrumb >>> li.active span:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li.active span:after {
    border-left-color: #19be6b;
    }
    #breadcrumb >>> li:first-child span {
    padding-left: 15px;
    -moz-border-radius: 4px 0 0 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px 0 0 4px;
    }
    #breadcrumb >>> li:first-child span:before {
    border: none;
    }
    /*
    #breadcrumb >>> li:last-child span {
    padding-right: 15px;
    -moz-border-radius: 0 4px 4px 0;
    -webkit-border-radius: 0;
    border-radius: 0 4px 4px 0;
    }
    #breadcrumb >>> li:last-child span:after {
    border: none;
    }
    */
    #breadcrumb >>> li span:before, #breadcrumb li span:after {
    content: "";
    position: absolute;
    top: 0;
    border: 0 solid #3498db;
    border-width: 20px 10px;
    width: 0;
    height: 0;
    }
    #breadcrumb >>> li span:before {
    left: -20px;
    border-left-color: transparent;
    }
    #breadcrumb >>> li span:after {
    left: 100%;
    border-color: transparent;
    border-left-color: #3498db;
    }
    #breadcrumb >>> li span:hover {
    background-color: #19be6b;
    }
    #breadcrumb >>> li span:hover:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li span:hover:after {
    border-left-color: #19be6b;
    }
    #breadcrumb >>> li span:active {
    background-color: #16a085;
    }
    #breadcrumb >>> li span:active:before {
    border-color: #16a085;
    border-left-color: transparent;
    }
    #breadcrumb >>> li span:active:after {
    border-left-color: #16a085;
    }

    .checkmark{
        z-index: 901;
        position: absolute;
        bottom: -8px;
        left: 15px;
        background: #19be6b;
        color: #fff;
        border-radius: 50%;
        font-size: 20px;
    }

</style>

<template>

    <Card :style="{ width: '100%' }">
        

        <!-- Loader -->
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading Lifecycle...</Loader>

        <Poptip word-wrap width="300" trigger="hover" :content="popTipTitle">

            <h4 class="mb-2" :style="{ fontSize: '1.5em' }">Lifecycle</h4>

            <ul id="breadcrumb">
                <li :class="localJobcard.has_approved ? 'active': ''"
                    @mouseover="popTipTitle = 'Jobcard has been approved'"
                    @click="updateSelectedStage(stage)">
                    <span>Open</span>
                </li>
                <li v-for="(stage, i) in localJobcard.lifecycle.stages" 
                    :class="isActiveStage(stage) ? 'active': ''"
                    @mouseover="popTipTitle = stage.description"
                    @click="updateSelectedStage(stage)"
                    :style="{ position: 'relative' }">
        
                    <Dropdown v-if="isActiveStage(stage) && ( dropdownOptions(stage) ).length && !isActiveStage(localJobcard.lifecycle.stages[i + 1])">
                        <a href="javascript:void(0)">
                            <span >{{ stage.name }}</span>
                            <Icon v-if="isActiveStage(stage) && dropdownOptions(stage)" class="checkmark" type="ios-arrow-dropdown" />
                        </a>
                        <DropdownMenu slot="list">
                            <DropdownItem v-for="(option, i) in dropdownOptions(stage)" :key="i"
                                          :style="{ width: '100%' }">{{ option }}
                            </DropdownItem>
                        </DropdownMenu>
                    </Dropdown>
                    <div v-else>
                        <span>{{ stage.name }}</span>
                        <Icon v-if="isActiveStage(stage)" class="checkmark" type="ios-checkmark-circle-outline" />
                    </div>

                </li>
            </ul>
            
        </Poptip>

        <Alert v-if="!isLoading && !(localJobcard.lifecycle_stages || []).length" type="warning">
            No Lifecycle Found
        </Alert>

        <!-- 
            MODAL TO CHANGE MOBILE MONEY ACCOUNT - VIA EMAIL
        -->
        <updateLifecycleStageModal 
            v-if="isOpenUpdateLifecycleStageModal" 
            :jobcard="localJobcard" 
            :selectedStage="localSelectedStage" 
            @visibility="isOpenUpdateLifecycleStageModal = $event"
            @updated="$emit('updated', $event)">
        </updateLifecycleStageModal>

    </Card>

</template>

<script>

    import Loader from './../loaders/Loader.vue';

    /*  Selectors   */
    import jobcardLifecycleStageSelector from './../selectors/jobcardLifecycleStageSelector.vue'; 

    /*  Modals  */
    import updateLifecycleStageModal from './../modals/updateLifecycleStageModal.vue';

    export default {
        props: {
            jobcard: {
                type: Object,
                default: null
            },
        },
        components: { Loader, jobcardLifecycleStageSelector, updateLifecycleStageModal },
        data(){
            return {
                localJobcard: this.jobcard,
                localSelectedStage: null,
                isOpenUpdateLifecycleStageModal: false,



                isLoading: false,
                lifecycle: {},
                popTipTitle: '',
                storedStage: {},
                storedNextStep: null,
                isOpenUpdateLifecycleModal: false,
                isOpenAddLifecycleModal: false,
            }
        },
        computed: {
            stages: function () {
                return (this.lifecycle.template || {}).sections;
            },
            activeStep: {
                get() {
                    return this.lifecycle.step;
                },
                set(newValue) {
                    this.lifecycle.step = newValue;
                }
            }

        },
        methods: {
            isActiveStage(stage){
                
                var active = false;
                
                if( stage ){

                    for(var x = 0; x < this.localJobcard.lifecycle_stages.length; x++){
                        console.log('Check....................................................');
                        
                        if( this.localJobcard.lifecycle_stages[x].type ==  stage.type){
                            console.log(this.localJobcard.lifecycle_stages[x].type +' == '+  stage.type);
                            active = true;
                        }
                        
                    }

                }

                return active;
            },
            getStageData(stageType){
                
                for(var x = 0; x < this.localJobcard.lifecycle_stages.length; x++){
                    
                    if( this.localJobcard.lifecycle_stages[x].type ==  stageType){
                        return this.localJobcard.lifecycle_stages[x];
                    }
                    
                }
                
            },
            dropdownOptions(stage){
                var options = [];
                
                if( stage.name == 'Deposit Paid' ){
                    options = ['Cancel Payment', 'Notify Client'];
                }else if( stage.name == 'Job Started' ){
                    options = ['Set Job to Pending', 'Cancel Job', 'Notify Client'];
                }
                return options;
            },
            updateSelectedStage(stage){
                
                if( stage.name == 'Deposit Paid' ){
                    this.localSelectedStage = this.getDepositPaidTemplate(stage);
                }else if( stage.name == 'Job Started' ){
                    this.localSelectedStage = this.getJobStartedTemplate(stage);
                }else if( stage.name == 'Closed' ){
                    this.localSelectedStage = this.getClosedTemplate(stage);
                }

                this.isOpenUpdateLifecycleStageModal = true;
            },
            getDepositPaidTemplate(stage){
                
                var stageData = this.getStageData(stage.type);

                var template = 
                        {   
                            type: stage.type, 
                            updated_stage_id: (stageData || {}).updated_stage_id,   
                            linked_invoice_id: (stageData || {}).linked_invoice_id,
                            currency_type: (stageData || {}).currency_type,
                            payment_amount: (stageData || {}).payment_amount,
                            payment_method: (stageData || {}).payment_method,
                            full_payment: false
                        }

                return template;
            },
            getJobStartedTemplate(stage){
              
                var stageData = this.getStageData(stage.type);

                var template = 
                        {
                            type: stage.type, 
                            updated_stage_id: (stageData || {}).updated_stage_id,   
                            linked_staff_ids: (stageData || {}).linked_staff_ids,
                            date_started: (stageData || {}).date_started,
                            time_started: (stageData || {}).time_started,
                    }

                return template;
            },
            getClosedTemplate(stage){

                var template = 
                        {
                            type: stage.type, 
                            updated_stage_id: null
                        }

                return template;
            },

            updateChanges(nextStep){
                this.activeStep = this.storedNextStep;
                this.closeModal();
            },
            updateAddLifecycleChanges(lifecycle){
                //  Update the local lifecycle data with our new data
                this.lifecycle = lifecycle;

                //  Select the first step
                this.activeStep = 1;

                //  Close modal
                this.closeAddLifecycleModal();
            },
            closeModal(){
                this.isOpenUpdateLifecycleModal = !this.isOpenUpdateLifecycleModal;
            },
            closeAddLifecycleModal(){
                this.isOpenAddLifecycleModal = !this.isOpenAddLifecycleModal;
            },
            showLifecycleStageModal(stage, step){
                this.storedNextStep = ++step;
                this.storedStage = stage; 
                this.isOpenUpdateLifecycleModal = true;
            }
        },
    };
    
</script>