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

    #breadcrumb >>> li.warning span {
    background-color: #f90;
    }
    #breadcrumb >>> li.warning span:before {
    border-color: #f90;
    border-left-color: transparent;
    }
    #breadcrumb >>> li.warning span:after {
    border-left-color: #f90;
    }

    #breadcrumb >>> li.danger span {
    background-color: #ed4014;
    }
    #breadcrumb >>> li.danger span:before {
    border-color: #ed4014;
    border-left-color: transparent;
    }
    #breadcrumb >>> li.danger span:after {
    border-left-color: #ed4014;
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
                        :class="determineStageColor(stage)"
                        @mouseover="popTipTitle = stage.description"
                        :style="{ position: 'relative' }">
                        
                        <div>
                            <span>{{ stage.name }}</span>
                            <Icon v-if="canShowCheckmark(stage)" 
                                    class="checkmark" type="ios-checkmark-circle-outline" />
                        </div>

                    </li>
                </ul>
                
            </Poptip>
            
            <div v-for="(stage, i_1) in localJobcard.lifecycle.stages" :key="i_1"
                 class="float-left mt-1 ml-2">
                 
                <Select v-if="isActiveStage(stage) && 
                              ( dropdownOptions(stage, i_1) ).length && !isActiveStage(localJobcard.lifecycle.stages[i_1 + 1])"
                        v-model="selectedTriggerName" style="width:150px"
                        @on-change="handleTrigger(stage)"
                        :key="selectedTriggerRenderKey"
                        placeholder="Next step">
                    <Option v-for="(option, i_2) in dropdownOptions(stage, i_1)" :key="i_2"
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
            canShowCheckmark(stage){

                var stageData = this.getJobStartedTemplate(stage)

                if( this.isActiveStage(stage) && !stageData.pending_status && !stageData.cancelled_status ){
                    return true;
                }

                return false;
            },
            determineStageColor(stage){

                var stageData = this.getJobStartedTemplate(stage)

                if( (stageData.cancelled_status || false) ){
                    return 'danger';
                }else if( (stageData.pending_status || false) ){
                    return 'warning';
                }else if( this.isActiveStage(stage) || (stage.type == 'open' && this.localJobcard.has_approved) ? 'active': '' ){
                    return 'active';
                }
            },
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
            dropdownOptions(stage, stageIndex){
                var stageData, nextStage, notifyClientText, options = [];
            
                //  Get the data associated with this step
                stageData = this.getStageData(stage);
                nextStage = this.localJobcard.lifecycle.stages[stageIndex + 1];
                
                if( stage.type == 'open' ){

                    options.push({ name: 'Proceed to ' + (nextStage || {}).name, triggerName: 'next_step', icon:'ios-redo-outline', divider:false });

                }else if( stage.type == 'payment' ){
                    
                    options.push({ name: 'Proceed to ' + (nextStage || {}).name, triggerName: 'next_step', icon:'ios-redo-outline', divider:false });
                                        
                    options.push({ name: 'Undo ' + stage.name, triggerName: 'undo_payment', icon:'ios-undo-outline', divider:true });
                              
                    options.push({ name: 'Notify Client', triggerName: 'notify_payment', icon:'ios-chatboxes-outline', divider:false });

                }else if( stage.type == 'job_started' ){

                    if( !stageData.pending_status && !stageData.cancelled_status ){
                        //  Option to move to the next stage
                        options.push({ name: 'Proceed to ' + (nextStage || {}).name, triggerName: 'next_step', icon:'ios-redo-outline', divider:false });
                    }

                    if( !stageData.pending_status && !stageData.cancelled_status ){
                        //  Option to undo
                        options.push({ name: 'Undo ' + stage.name, triggerName: 'undo_job_started', icon:'ios-undo-outline', divider:true });
                    }

                    if( !stageData.cancelled_status ){

                        if( stageData.pending_status ){
                            //  Option to set the job to pending
                            options.push({ name: 'Resume Job', triggerName: 'reverse_pending_job', icon:'ios-repeat', divider:false });
                        }else{
                            //  Option to set the job to pending
                            options.push({ name: 'Set Job To Pending', triggerName: 'pending_job_started', icon:'ios-time-outline', divider:false });      
                        }

                    }

                    if( stageData.cancelled_status ){
                        //  Option to reverse a cancelled job
                        options.push({ name: 'Re-open Job', triggerName: 'reverse_cancel_job', icon:'ios-repeat', divider:true });
                    }else{
                        //  Option to set the job to cancelled
                        options.push({ name: 'Cancel Job Completely', triggerName: 'cancel_job_started', icon:'ios-hand-outline', divider:true });
                    }

                    //  Notify Client Text

                    if( stageData.cancelled_status ){
                        notifyClientText = 'Notify Client (Job Cancelled)';
                    }else if( stageData.pending_status ){
                        notifyClientText = 'Notify Client (Job Pending)';
                    }else{
                        notifyClientText = 'Notify Client (Job Started)';
                    }

                    //  Option to notify client on progress
                    options.push({ name: notifyClientText, triggerName: 'notify_payment', icon:'ios-chatboxes-outline', divider:false });
                              
                }else if( stage.type == 'closed' ){
                    
                    options.push({ name: 'Undo Close', triggerName: 'undo_close', icon:'ios-undo-outline', divider:false });

                    options.push({ name: 'Notify client', triggerName: 'notify_payment', icon:'ios-chatboxes-outline', divider:false });

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

                return this.localSelectedStage;

            },
            getDepositPaidTemplate(stage){
                
                var stageData = this.getStageAsRecentActivity(stage);
                
                var template = 
                        {   
                            type: stage.type, 
                            instance: stage.instance, 
                            updated_stage_id: ((stageData || {}).activity || {}).updated_stage_id || (stageData || {}).id || null,   
                            linked_invoice_id: ((stageData || {}).activity || {}).linked_invoice_id,
                            currency_type: ((stageData || {}).activity || {}).currency_type,
                            payment_amount: ((stageData || {}).activity || {}).payment_amount,
                            payment_method: ((stageData || {}).activity || {}).payment_method,
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
                            pending_status: (stageData || {}).pending_status || false,
                            cancelled_status: (stageData || {}).cancelled_status || false,
                            notified_client_status: (stageData || {}).cancelled_status || false,
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
                    
                    let stageData = this.updateSelectedStage(stage);

                    let stageId = ( this.getStageAsRecentActivity(stage) || {}).id;

                    let triggerName = this.selectedTriggerName;

                    let makeApi = false;

                    this.selectedTriggerName = '';

                    this.selectedTriggerRenderKey = this.selectedTriggerRenderKey + 1;

                    if( triggerName ==  'undo_payment' || triggerName ==  'undo_job_started' || triggerName ==  'undo_close'){
                       if(stageId){
                            makeApi = true;
                            var url = '/api/jobcards/' + this.localJobcard.id + '/lifecycle/stages/undo';
                       }
                    }else if( triggerName ==  'next_step' ){

                        if( stage ){

                            for(var x = 0; x < this.localJobcard.lifecycle.stages.length; x++){
                                
                                if( this.localJobcard.lifecycle.stages[x].type ==  stage.type && 
                                    this.localJobcard.lifecycle.stages[x].instance ==  stage.instance ){

                                        this.updateSelectedStage(this.localJobcard.lifecycle.stages[x + 1]);
                                        this.isOpenUpdateLifecycleStageModal = true;
                                
                                }
                                
                            }

                        }

                    }else if( triggerName ==  'pending_job_started' ){
                        
                        stageData.pending_status = true;

                        makeApi = true;
                        var url = '/api/jobcards/' + this.localJobcard.id + '/lifecycle/stages';

                    }else if( triggerName ==  'reverse_pending_job'){

                        stageData.cancelled_status = false;
                        stageData.pending_status = false;

                        makeApi = true;
                        var url = '/api/jobcards/' + this.localJobcard.id + '/lifecycle/stages';

                    }else if( triggerName ==  'cancel_job_started' ){

                        stageData.cancelled_status = true;

                        makeApi = true;
                        var url = '/api/jobcards/' + this.localJobcard.id + '/lifecycle/stages';         

                    }else if( triggerName ==  'reverse_cancel_job'){

                        stageData.cancelled_status = false;
                        stageData.pending_status = false;

                        makeApi = true;
                        var url = '/api/jobcards/' + this.localJobcard.id + '/lifecycle/stages';

                    }else if( triggerName ==  'notify_payment' ){

                        

                    }
                    
                    if( makeApi ){

                        var self = this;

                        //  Start loader
                        this.isSaving = true;

                        stageData = { stage: stageData };

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