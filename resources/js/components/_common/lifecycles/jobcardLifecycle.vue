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
        <h4 class="mb-2" :style="{ fontSize: '1.5em' }">Lifecycle</h4>

        <div>

            <Poptip word-wrap width="300" trigger="hover" :content="popTipTitle" class="float-left">

                <ul v-if="(localJobcard.lifecycle.stages || {}).length" id="breadcrumb">
                    <li v-for="(stage, i) in localJobcard.lifecycle.stages" 
                        :class="isActiveStage(stage) || (stage.type == 'open' && localJobcard.has_approved) ? 'active': ''"
                        @mouseover="popTipTitle = stage.description"
                        @click="updateSelectedStage(stage)"
                        :style="{ position: 'relative' }">
                        
                        <div>
                            <span>{{ stage.name }}</span>
                            <Icon v-if="isActiveStage(stage)" 
                                    class="checkmark" type="ios-checkmark-circle-outline" />
                        </div>

                    </li>
                </ul>
                
            </Poptip>
            
            <div v-for="(stage, i) in localJobcard.lifecycle.stages" :key="i"
                 class="float-left mt-1 ml-2">
                 
                <Select v-if="isActiveStage(stage) && ( dropdownOptions(stage) ).length && !isActiveStage(localJobcard.lifecycle.stages[i + 1])"
                        v-model="selectedTriggerName" style="width:150px"
                        @on-change="handleTrigger(stage)"
                        :key="selectedTriggerRenderKey"
                        placeholder="Next step">
                    <Option v-for="(option, i) in dropdownOptions(stage)" :key="i"
                            :class="option.divider ? 'border-bottom' : ''" 
                            :value="option.triggerName">
                            <Icon v-if="option.icon" :type="option.icon" :size="20" class="mr-1"/>
                            <span>{{ option.name }}</span>
                    </Option>
                </Select>

            </div>

        </div>

        <div class="clearfix"></div>

        <Alert v-if="!(localJobcard.lifecycle.stages || {}).length" type="warning">
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
            @updated="updateJobcardLifecycle($event)">
        </updateLifecycleStageModal>

    </Card>

</template>

<script> 

    /*  Modals  */
    import updateLifecycleStageModal from './../modals/updateLifecycleStageModal.vue';

    export default {
        props: {
            jobcard: {
                type: Object,
                default: null
            },
        },
        components: { updateLifecycleStageModal },
        data(){
            return {
                localJobcard: this.jobcard,
                localSelectedStage: null,
                isOpenUpdateLifecycleStageModal: false,
                selectedTriggerName: '',
                selectedTriggerRenderKey: 0,
                popTipTitle: ''
            }
        },
        methods: {
            updateJobcardLifecycle(updatedJobcard){
                //  Update lifecycle stages
                this.localJobcard.lifecycle_stages = updatedJobcard.lifecycle_stages;

                //  Close the modal
                this.isOpenUpdateLifecycleStageModal = false;
            },
            isActiveStage(stage){
                
                var active = false;
                
                if( stage ){

                    for(var x = 0; x < this.localJobcard.lifecycle_stages.length; x++){
                        if( this.localJobcard.lifecycle_stages[x].activity.type ==  stage.type && 
                            this.localJobcard.lifecycle_stages[x].activity.instance ==  stage.instance ){
                            active = true;
                        }
                        
                    }

                    if( !active ){
                        if(stage.type ==  'open' && this.localJobcard.has_approved){
                            active = true;
                        }
                    }

                }

                return active;
            },
            getStageData(stage){
                
                for(var x = 0; x < this.localJobcard.lifecycle_stages.length; x++){
                    
                    if( this.localJobcard.lifecycle_stages[x].activity.type ==  stage.type && 
                        this.localJobcard.lifecycle_stages[x].activity.instance ==  stage.instance ){
                        return this.localJobcard.lifecycle_stages[x].activity;
                    }
                    
                }
                
            },
            getStageAsRecentActivity(stage){
                
                for(var x = 0; x < this.localJobcard.lifecycle_stages.length; x++){
                    
                    if( this.localJobcard.lifecycle_stages[x].activity.type ==  stage.type && 
                        this.localJobcard.lifecycle_stages[x].activity.instance ==  stage.instance ){
                        return this.localJobcard.lifecycle_stages[x];
                    }
                    
                }
                
            },
            dropdownOptions(stage){
                var options = [];
                
                if( stage.type == 'open' ){
                    options = [{
                                name: 'Next Step',
                                triggerName: 'next_step',
                                icon:'ios-redo-outline',
                                divider:false
                              }];
                }else if( stage.type == 'payment' ){
                    options = [{
                                name: 'Next Step',
                                triggerName: 'next_step',
                                icon:'ios-redo-outline',
                                divider:false
                              },{
                                name: 'Undo ' + stage.name,
                                triggerName: 'undo_payment',
                                icon:'ios-undo-outline',
                                divider:true
                              },{
                                name: 'Notify Client',
                                triggerName: 'notify_payment',
                                icon:'ios-chatboxes-outline',
                                divider:false
                              }];
                }else if( stage.type == 'job_started' ){
                    options = [{
                                name: 'Next Step',
                                triggerName: 'next_step',
                                icon:'ios-redo-outline',
                                divider:false
                              },{
                                name: 'Undo ' + stage.name,
                                triggerName: 'undo_job_started',
                                icon:'ios-undo-outline',
                                divider:true
                              },{
                                name: 'Set Job To Pending',
                                triggerName: 'pending_job_started',
                                icon:'ios-time-outline',
                                divider:false
                              },{
                                name: 'Cancel Job Completely',
                                triggerName: 'cancel_job_started',
                                icon:'ios-hand-outline',
                                divider:true
                              },{
                                name: 'Notify Client',
                                triggerName: 'notify_payment',
                                icon:'ios-chatboxes-outline',
                                divider:false
                              }];
                }else if( stage.type == 'closed' ){
                    options = [{
                                name: 'Undo Close',
                                triggerName: 'undo_close',
                                icon:'ios-undo-outline',
                                divider:false
                              },{
                                name: 'Notify client',
                                triggerName: 'notify_payment',
                                icon:'ios-chatboxes-outline',
                                divider:false
                              }];
                }
                return options;
            },
            updateSelectedStage(stage){
                
                if( stage.type == 'payment' ){
                    this.localSelectedStage = this.getDepositPaidTemplate(stage);
                }else if( stage.type == 'job_started' ){
                    this.localSelectedStage = this.getJobStartedTemplate(stage);
                }else if( stage.type == 'closed' ){
                    this.localSelectedStage = this.getClosedTemplate(stage);
                }

                this.isOpenUpdateLifecycleStageModal = true;
            },
            getDepositPaidTemplate(stage){
                
                var stageData = this.getStageData(stage);
                
                var template = 
                        {   
                            type: stage.type, 
                            instance: stage.instance, 
                            updated_stage_id: (stageData || {}).updated_stage_id || null,   
                            linked_invoice_id: (stageData || {}).linked_invoice_id,
                            currency_type: (stageData || {}).currency_type,
                            payment_amount: (stageData || {}).payment_amount,
                            payment_method: (stageData || {}).payment_method,
                            full_payment: false
                        }

                return template;
            },
            getJobStartedTemplate(stage){
              
                var stageData = this.getStageData(stage);

                var template = 
                        {
                            type: stage.type, 
                            instance: stage.instance, 
                            updated_stage_id: (stageData || {}).updated_stage_id || null,   
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
                            instance: stage.instance, 
                            updated_stage_id: null
                        }

                return template;
            },
            handleTrigger(stage){

                if( stage ){
                    
                    let stageData = null;

                    let stageId = ( this.getStageAsRecentActivity(stage) || {}).id;

                    let triggerName = this.selectedTriggerName;

                    let makeApi = false;

                    this.selectedTriggerName = '';

                    this.selectedTriggerRenderKey = this.selectedTriggerRenderKey + 1;

                    if( triggerName ==  'undo_payment' || triggerName ==  'undo_job_started' || triggerName ==  'undo_close'){
                       if(stageId){
                            makeApi = true;
                            var url = '/api/jobcards/' + this.localJobcard.id + '/lifecycle/stages/'+stageId+'/undo';
                       }
                    }else if( triggerName ==  'next_step' ){

                        if( stage ){

                            for(var x = 0; x < this.localJobcard.lifecycle.stages.length; x++){
                                
                                if( this.localJobcard.lifecycle.stages[x].type ==  stage.type && 
                                    this.localJobcard.lifecycle.stages[x].instance ==  stage.instance ){

                                        this.updateSelectedStage(this.localJobcard.lifecycle.stages[x + 1]);
                                
                                }
                                
                            }

                        }

                    }
                    
                    if( makeApi ){

                        var self = this;

                        //  Start loader
                        this.isSaving = true;

                        console.log('Attempt to handle jobcard lifecycle trigger...');

                        //  Use the api call() function located in resources/js/api.js
                        api.call('post', url, stageData)
                            .then(({ data }) => {
                                
                                console.log(data);

                                //  Stop loader
                                self.isSaving = false;
                                
                                //  Alert creation success
                                self.$Message.success('Lifecycle updated sucessfully!');

                                self.updateJobcardLifecycle(data);

                            })         
                            .catch(response => { 
                                //  Stop loader
                                self.isSaving = false;

                                console.log('jobcardLifecycle.vue - Error updating jobcard lifecycle trigger...');
                                console.log(response);
                            });

                    }

                }

            }
        },
    };
    
</script>