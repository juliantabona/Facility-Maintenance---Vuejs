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

    /*   WARNING PULSE   */
    
    #breadcrumb >>> li.pulse span,
    #breadcrumb >>> li.pulse i{
        animation: warningPulseSpan 1.5s linear infinite;
    }

    @-webkit-keyframes warningPulseSpan {
        0% {
            background: #f90;
        }
        50% {
            background: #ffd493;
        }
        100% {
            background: #f90;
        }
    }

    @keyframes warningPulseSpan {
        0% {
            background: #f90;
        }
        50% {
            background: #ffd493;
        }
        100% {
            background: #f90;
        }
    }

    #breadcrumb >>> li.pulse span:before {
        animation: warningPulseSpanBefore 1.5s linear infinite;
    }

    @-webkit-keyframes warningPulseSpanBefore {
        0% {
            border-color: #f90;
            border-left-color: transparent;
        }
        50% {
            border-color: #ffd493;
            border-left-color: transparent;
        }
        100% {
            border-color: #f90;
            border-left-color: transparent;
        }
    }

    @keyframes warningPulseSpanBefore {
        0% {
            border-color: #f90;
            border-left-color: transparent;
        }
        50% {
            border-color: #ffd493;
            border-left-color: transparent;
        }
        100% {
            border-color: #f90;
            border-left-color: transparent;
        }
    }

    #breadcrumb >>> li.pulse span:after {
        animation: warningPulseSpanAfter 1.5s linear infinite;
    }

    @-webkit-keyframes warningPulseSpanAfter {
        0% {
            border-left-color: #f90;
        }
        50% {
            border-left-color: #ffd493;
        }
        100% {
            border-left-color: #f90;
        }
    }

    @keyframes warningPulseSpanAfter {
        0% {
            border-left-color: #f90;
        }
        50% {
            border-left-color: #ffd493;
        }
        100% {
            border-left-color: #f90;
        }
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

    /*  
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
    */

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

    .warning-mark{
        z-index: 901;
        position: absolute;
        bottom: -8px;
        left: 15px;
        background: #f90;
        color: #fff;
        border-radius: 50%;
        font-size: 20px;
    }

    .danger-mark{
        z-index: 901;
        position: absolute;
        bottom: -8px;
        left: 15px;
        background: #ed4014;
        color: #fff;
        border-radius: 50%;
        font-size: 20px;
    }



    .lifecycle-toggle-btn >>> .ivu-select-dropdown {
        max-height: 250px !important;
    }

</style>

<template>

    <div>

        <div v-if="lifecycleStages.length" class="clearfix">

            <!-- Lifecycle -->
            <div class="float-left" style="position:relative;">
                <!-- White overlay when saving lifecycle -->
                <Spin size="large" fix v-if="isSaving" style="border-radius: 4px;z-index: 902;"></Spin>

                <!-- Lifecycle Stages -->
                <Poptip word-wrap width="300" trigger="hover" :content="popTipTitle" class="float-left">

                    <ul v-if="lifecycleStages.length" id="breadcrumb" class="lifecycle-container">
                        <li v-for="(stage, i) in lifecycleStages" 
                            :class="determineStageColor(stage)"
                            @mouseover="popTipTitle = determineStageDescription(stage)"
                            :style="{ position: 'relative' }">
                            
                            <div>
                                <span>{{ determineStageName(stage) }}</span>
                                <Icon v-if="canShowCheckmark(stage)" class="checkmark" type="ios-checkmark-circle-outline" />
                                <Icon v-if="canShowWarningMark(stage)" class="warning-mark" type="ios-alert-outline" />
                                <Icon v-if="canShowDangerMark(stage)" class="danger-mark" type="ios-close-circle-outline" />
                            </div>
                        </li>
                    </ul>
                    
                </Poptip>

            </div>
            
            <!-- Next Step Button -->
            <div v-if="canEditLifecycle && !isSaving" v-for="(stage, i) in lifecycleStages" :key="i" class="lifecycle-toggle-btn float-left mt-1 ml-2">
                
                <Select v-if="isActiveStage(stage) && 
                            ( dropdownOptions(stage, i) ).length && !isActiveStage(lifecycleStages[i + 1])"
                        v-model="selectedTriggerName" style="width:150px"
                        @on-change="handleTrigger(lifecycleStages, i)"
                        :key="selectedTriggerRenderKey"
                        placeholder="Next step">
                    <Option v-for="(option, i_2) in dropdownOptions(stage, i)" :key="i_2"
                            :class="option.divider ? 'border-bottom' : ''" 
                            :value="option.triggerName">
                            <Icon v-if="option.icon" :type="option.icon" :size="20" class="mr-1"/>
                            <span>{{ option.name }}</span>
                    </Option>
                </Select>

            </div>

            <!-- Loader -->
            <Loader v-if="isSaving" :loading="true" type="text" class="text-left" theme="white">Updating status...</Loader>

        </div>

        <Alert v-if="!lifecycleStages.length" type="warning">
            No Lifecycle Found
        </Alert>

        <!-- 
            MODAL TO CHANGE MOBILE MONEY ACCOUNT - VIA EMAIL
        -->
        <updateLifecycleStageModal 
            v-if="isOpenUpdateLifecycleStageModal" 
            :model="localModel" 
            :postURL="postURL"
            :modelId="modelId"
            :modelType="modelType"
            :selectedStage="localSelectedStage" 
            @visibility="isOpenUpdateLifecycleStageModal = $event"
            @updated="updateLifecycle($event)">
        </updateLifecycleStageModal>

    </div>

</template>

<script> 

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue'; 

    /*  Modals  */
    import updateLifecycleStageModal from './../modals/updateLifecycleStageModal.vue';

    export default {
        props: {
            model: {
                type: Object,
                default: null
            },
            postURL:{
                type: String,
                default: '/api/lifecycles/stage'
            },
            modelId:{
                type: Number,
                default: null
            },
            modelType:{
                type: String,
                default: null
            },
            resourceName:{
                type: String,
                default: ''
            },
            canEditLifecycle: {
                type: Boolean,
                default: true
            }
        },
        components: { Loader, updateLifecycleStageModal },
        data(){
            return {
                localModel: this.model,
                lifecycleStages: (((this.model.lifecycle || [])[0] || {}).stages || []),
                localSelectedStage: null,
                isOpenUpdateLifecycleStageModal: false,
                selectedTriggerName: '',
                selectedTriggerRenderKey: 0,
                popTipTitle: '',
                isSaving: false
            }
        },
        methods: {
            canShowCheckmark(stage){

                var stageData = this.getBasicTemplate(stage)

                if( this.isActiveStage(stage) && !stageData.pending_status && 
                    !stageData.cancelled_status && !stageData.manual_verification_status ){
                    return true;
                }

                return false;
            },
            canShowWarningMark(stage){

                var stageData = this.getBasicTemplate(stage)

                if( stageData.pending_status || stageData.manual_verification_status ){
                    return true;
                }
                
                return false;
            },
            canShowDangerMark(stage){

                var stageData = this.getBasicTemplate(stage)

                if( stageData.cancelled_status ){
                    return true;
                }
                
                return false;
            },
            determineStageColor(stage){

                var stageData = this.getBasicTemplate(stage);
                var classes = '';

                if( (stageData.cancelled_status || false) ){
                    classes = 'danger';
                }else if( (stageData.pending_status || false) || (stageData.manual_verification_status || false) ){
                    classes = 'warning';
                }else if( this.isActiveStage(stage) || (stage.type == 'open') ){
                    classes = 'active';
                }
                
                //  Add the opacity pulse effect
                if( (stageData.manual_verification_status || false) ){
                    classes = classes + ' pulse';
                }

                return classes;
            },
            determineStageName(stage){

                var stageData = this.getBasicTemplate(stage)

                if( stageData.cancelled_status ){
                    return 'Cancelled';
                }else if( stageData.pending_status ){
                    if(stageData.type == 'payment'){
                        return 'Pending Payment';
                    }else if(stageData.type == 'delivery'){
                        return 'Pending Delivery';
                    }else if(stageData.type == 'job_started'){
                        return 'Pending Job';
                    }
                }else if( stageData.manual_verification_status ){
                    if(stageData.type == 'payment'){
                        return this.canEditLifecycle ? 'Verify Payment' : 'Payment Waiting Verification';
                    }else if(stageData.type == 'delivery'){
                        return this.canEditLifecycle ? 'Verify Delivery' : 'Delivery Waiting Verification';
                    }
                }else if( stageData.skip_status ){
                    if(stageData.type == 'payment'){
                        return 'Skipped Payment';
                    }else if(stageData.type == 'delivery'){
                        return 'Skipped Delivery';
                    }
                }
                
                return stage.name;
            },
            determineStageDescription(stage){

                var stageData = this.getBasicTemplate(stage);
                var isActive = this.isActiveStage(stage);

                if( stageData.cancelled_status ){
                    return 'The '+this.resourceName+' is currently cancelled';
                }else if( stageData.pending_status ){
                    if(stageData.type == 'payment'){
                        return 'The '+this.resourceName+' is currently pending payment';
                    }else if(stageData.type == 'delivery'){
                        return 'The '+this.resourceName+' is currently pending delivery';
                    }else if(stageData.type == 'job_started'){
                        return 'Pending Job';
                    }
                }else if( stageData.manual_verification_status ){
                    if(stageData.type == 'payment'){
                        return 'The '+this.resourceName+' payment needs to be verified manually';
                    }else if(stageData.type == 'delivery'){
                        return 'The '+this.resourceName+' delivery needs to be verified manually';
                    }
                }else if( stageData.skip_status ){
                    if(stageData.type == 'payment'){
                        return 'The '+this.resourceName+' payment by customer was skipped';
                    }else if(stageData.type == 'delivery'){
                        return 'The '+this.resourceName+' delivery to customer was skipped';
                    }
                }else if( !isActive ){
                    if(stageData.type == 'payment'){
                        return 'The '+this.resourceName+' payment is yet to be settled';
                    }else if(stageData.type == 'delivery'){
                        return 'The '+this.resourceName+' is yet to be delivered';
                    }else if(stageData.type == 'job_started'){
                        return 'The job is yet to be started';
                    }else if(stageData.type == 'closed'){
                        return 'The '+this.resourceName+' is yet to be completed';
                    }
                }
                
                return stage.description;
            },
            updateLifecycle(updatedModel){
                //  Update lifecycle stages
                this.localModel = updatedModel;

                //  Notify parent
                this.$emit('updated:lifecycle', updatedModel);

                //  Close the modal
                this.isOpenUpdateLifecycleStageModal = false;
            },
            isActiveStage(stage){
                
                var active = false;
                
                if( stage ){

                    for(var x = 0; x < this.localModel.lifecycle_stages.length; x++){
                        if( this.localModel.lifecycle_stages[x].activity.type ==  stage.type && 
                            this.localModel.lifecycle_stages[x].activity.instance ==  stage.instance){
                            active = true;
                        }
                    }

                    if( !active ){
                        if(stage.type ==  'open'){
                            active = true;
                        }
                    }

                }

                return active;
            },
            getStageData(stage){
                
                for(var x = 0; x < this.localModel.lifecycle_stages.length; x++){
                    
                    if( this.localModel.lifecycle_stages[x].activity.type ==  stage.type && 
                        this.localModel.lifecycle_stages[x].activity.instance ==  stage.instance ){
                        return this.localModel.lifecycle_stages[x].activity;
                    }
                    
                }

                return {}
                
            },
            getStageAsRecentActivity(stage){
                
                for(var x = 0; x < this.localModel.lifecycle_stages.length; x++){
                    
                    if( this.localModel.lifecycle_stages[x].activity.type ==  stage.type && 
                        this.localModel.lifecycle_stages[x].activity.instance ==  stage.instance ){
                        return this.localModel.lifecycle_stages[x];
                    }
                    
                }
                
            },
            dropdownOptions(stage, stageIndex){
                var stageData, nextStage, notifyClientText, options = [];
            
                //  Get the data associated with this step
                stageData = this.getStageData(stage);
                nextStage = this.lifecycleStages[stageIndex + 1];
            
                //  If the current stage is pending or cancelled
                if( stageData.pending_status || stageData.cancelled_status || stageData.manual_verification_status ){
                 
                    /*************************************************
                    /   CURRENT STEP CANCELLED OR PENDING OPTIONS    *
                    **************************************************/

                    if( !stageData.cancelled_status && stageData.pending_status ){
                        //  Option to set to pending stage
                        notifyClientText = 'Notify Client (Pending)';
                        options.push({ name: 'Resume '+this.resourceName, triggerName: 'undo_step', icon:'ios-repeat', divider:false });
                    }

                    if( stageData.cancelled_status ){
                        //  Option to reverse a cancelled stage
                        notifyClientText = 'Notify Client (Cancelled)';
                        options.push({ name: 'Re-open '+this.resourceName, triggerName: 'reverse_cancel', icon:'ios-repeat', divider:true });
                    }

                    if( stageData.manual_verification_status ){
                        //  Option to verify a stage
                        notifyClientText = 'Notify Client (Verified)';

                        if(stageData.type == 'payment'){
                            options.push({ name: 'Approve Payment', triggerName: 'edit_step', icon:'ios-checkmark', divider:false });
                            options.push({ name: 'Decline Payment', triggerName: 'reverse_manual_verification', icon:'ios-close', divider:true });
                        }else if(stageData.type == 'delivery'){
                            options.push({ name: 'Approve Delivery', triggerName: 'edit_step', icon:'ios-checkmark', divider:false });
                            options.push({ name: 'Decline Delivery', triggerName: 'reverse_manual_verification', icon:'ios-close', divider:true });
                        }
                    }
                 
                }else{

                    /******************************************
                    /   PROCEED OPTION                        *
                    ******************************************/

                   //   If the current stage is not of type closed then show the proceed option
                    if(stage.type != 'closed'){
                        options.push({ name: 'Proceed to ' + (nextStage || {}).name, triggerName: 'next_step', icon:'ios-redo-outline', divider:false });
                    }

                    /******************************************
                    /   UNDO OPTION                           *
                    ******************************************/

                   //   If the current stage is not of type open then show the undo option
                    if(stage.type != 'open'){
                        let undoText = (stageData.skip_status == true) ? 'Undo Skip' : 'Undo ' + stage.name;
                        options.push({ name: undoText, triggerName: 'undo_step', icon:'ios-undo-outline', divider:true });
                    }

                    /******************************************
                    /   UNDO OPTION                           *
                    ******************************************/

                   //   If the current stage is not of type open or closed then show the edit option
                    if(stage.type == 'payment'){
                        options.push({ name: 'Edit Payment Details', triggerName: 'edit_step', icon:'ios-create-outline', divider:false });
                    }else if(stage.type == 'delivery'){
                        options.push({ name: 'Edit Delivery Details', triggerName: 'edit_step', icon:'ios-create-outline', divider:false });
                    }else if(stage.type == 'job_started'){
                        options.push({ name: 'Edit Job Started Details ', triggerName: 'edit_step', icon:'ios-create-outline', divider:false });
                    }

                    /******************************************
                    /   PENDING OR SKIP NEXT STAGE OPTIONS    *
                    ******************************************/

                    //  If the next stage is a payment stage
                    if( ( nextStage || {}).type == 'payment'){

                        //  Show the set to pending payment option
                        options.push({ name: 'Set As Pending Payment', triggerName: 'pending', icon:'ios-time-outline', divider:true });      
                    
                        //  Show the skip payment option
                        options.push({ name: 'Skip Payment', triggerName: 'skip', icon:'ios-fastforward-outline', divider:false });      
                    
                    //  If the next stage is a delivery stage
                    }else if( ( nextStage || {}).type == 'delivery'){

                        //  Show the set to pending payment option
                        options.push({ name: 'Set As Pending Delivery', triggerName: 'pending', icon:'ios-time-outline', divider:true });      
                    
                        //  Show the skip payment option
                        options.push({ name: 'Skip Delivery', triggerName: 'skip', icon:'ios-fastforward-outline', divider:false });      
                    
                    //  If the next stage is a job started stage
                    }else if( ( nextStage || {}).type == 'job_started'){

                        //  Show the set to pending payment option
                        options.push({ name: 'Set As Pending Job', triggerName: 'pending', icon:'ios-time-outline', divider:true });      
                    
                        //  Show the skip payment option
                        options.push({ name: 'Skip Job Started', triggerName: 'skip', icon:'ios-fastforward-outline', divider:false });      
                          
                    }

                    /******************************************
                    /   CANCEL OPTION                         *
                    ******************************************/
                    
                    //   If the current stage is not of type closed then show the cancel option
                    if(stage.type != 'closed'){
                        options.push({ name: 'Set As Cancelled', triggerName: 'cancel', icon:'ios-hand-outline', divider:false });
                    }
                }

                /******************************************
                /   NOTIFY CLIENT OPTION                  *
                ******************************************/
                if( notifyClientText ){
                    options.push({ name: notifyClientText, triggerName: 'notify_client', icon:'ios-chatboxes-outline', divider:false });
                }
                

                return options;
            },
            updateSelectedStage(stage, overides){
                if( stage.type == 'payment' ){
                    this.localSelectedStage = this.getPaymentTemplate(stage, overides);
                }else if( stage.type == 'delivery' ){
                    this.localSelectedStage = this.getDeliveryTemplate(stage, overides);
                }else if( stage.type == 'job_started' ){
                    this.localSelectedStage = this.getJobStartedTemplate(stage, overides);
                }else if( stage.type == 'open' || stage.type == 'closed' ){
                    this.localSelectedStage = this.getBasicTemplate(stage, overides);
                }

                return this.localSelectedStage;

            },
            getBasicTemplate(stage, overides){
                
                var stageData = this.getStageAsRecentActivity(stage);
                
                var template = 
                        {   
                            type: stage.type, 
                            instance: stage.instance, 
                            updated_stage_id: ((stageData || {}).activity || {}).updated_stage_id || (stageData || {}).id || null,   
                        }

                var templateStatuses = this.getBasicTemplateStatuses(stageData);

                template =  {...template, ...templateStatuses };

                console.log('Before Overide');
                console.log(template);

                if( overides ){

                    template =  {...template, ...overides };

                }

                console.log('After Overide');
                console.log(template);

                return template;
            },
            getBasicTemplateStatuses(stageData){
                return {
                        //  Skip status variables
                        skip_status: ((stageData || {}).activity || {}).skip_status || false,
                        skip_status_reason: ((stageData || {}).activity || {}).skip_status_reason || null,
                        other_skip_status_reason: ((stageData || {}).activity || {}).other_skip_status_reason || null,

                        //  Manual verification status variables
                        manual_verification_status: ((stageData || {}).activity || {}).manual_verification_status || false,
                        
                        //  Pending status variables
                        pending_status: ((stageData || {}).activity || {}).pending_status || false,
                        pending_status_reason: ((stageData || {}).activity || {}).pending_status_reason || null,
                        other_pending_status_reason: ((stageData || {}).activity || {}).other_pending_status_reason || null,

                        //  Cancel status variables
                        cancelled_status: ((stageData || {}).activity || {}).cancelled_status || false,
                        cancelled_status_reason: ((stageData || {}).activity || {}).cancelled_status_reason || null,
                        other_cancelled_status_reason: ((stageData || {}).activity || {}).other_cancelled_status_reason || null,

                        //  Notify client status variables
                        notified_client_status: ((stageData || {}).activity || {}).cancelled_status || false
                }
            },
            getPaymentTemplate(stage, overides){
                
                var stageData = this.getStageData(stage);
                
                var template = this.getBasicTemplate(stage, overides);

                var metaData = 
                        {   meta: {
                                linked_invoice_id: ((stageData || {}).meta || {}).linked_invoice_id || null,
                                currency_type: ((stageData || {}).meta || {}).currency_type || null,
                                payment_amount: ((stageData || {}).meta || {}).payment_amount || null,
                                payment_method: ((stageData || {}).meta || {}).payment_method || null,
                                other_payment_method: ((stageData || {}).meta || {}).other_payment_method || null,
                                full_payment: false
                            }
                        }
                

                return  {...template, ...metaData };
            },
            getDeliveryTemplate(stage, overides){
                
                var stageData = this.getStageData(stage);
                
                var template = this.getBasicTemplate(stage, overides);

                var metaData = 
                        {   meta: {
                                courier: ((stageData || {}).meta || {}).courier || null,
                                other_courier: ((stageData || {}).meta || {}).other_courier || null,
                                date_delivered: ((stageData || {}).meta || {}).date_delivered || null,
                                time_delivered: ((stageData || {}).meta || {}).time_delivered || null,
                            }
                        }
                
                return  {...template, ...metaData };
            },
            getJobStartedTemplate(stage, overides){
              
                var stageData = this.getStageData(stage);
                
                var template = this.getBasicTemplate(stage, overides);

                var metaData = 
                        {   meta: {
                                linked_staff_ids: ((stageData || {}).meta || {}).linked_staff_ids || [],
                                date_started: ((stageData || {}).meta || {}).date_started || null,
                                time_started: ((stageData || {}).meta || {}).time_started || null
                            }
                        }
                
                return  {...template, ...metaData };
                
            },
            handleTrigger(stages, index){
                
                var currentStage = stages[index];
                var stageData = null;
                var makeApi = true;
                var postURL = this.postURL;
                var triggerName = this.selectedTriggerName;
                var overides = this.getBasicTemplateStatuses();

                this.selectedTriggerName = '';
                this.selectedTriggerRenderKey = this.selectedTriggerRenderKey + 1;

                //  These affect the next stage in the lifecycle
                if( ['next_step', 'pending', 'skip'].includes(triggerName) ){
                    console.log('TRIGGER NAME: '+triggerName);

                    //  Overide the following status
                    if( triggerName ==  'pending'){
                        overides['pending_status'] = true;
                    }else if( triggerName ==  'skip'){
                        overides['skip_status'] = true;
                    }

                    stageData = this.updateSelectedStage(stages[index + 1], overides);

                    //  Open the modal
                    this.isOpenUpdateLifecycleStageModal = true;

                    //  Do not call the Api on this method
                    makeApi = false;

                //  These affect the current stage in the lifecycle
                }else if( ['cancel'].includes(triggerName) ){
                    console.log('TRIGGER NAME: '+triggerName);
                    //  Overide the following status
                    overides['cancelled_status'] = true;

                    console.log('overides 1');
                    console.log(overides);

                    stageData = this.updateSelectedStage(stages[index], overides);
                    
                    console.log('stageData');
                    console.log(stageData);

                    //  Open the modal
                    this.isOpenUpdateLifecycleStageModal = true;

                    //  Do not call the Api on this method
                    makeApi = false;
                    
                }else{

                    stageData = this.updateSelectedStage(stages[index]);

                }

                if( ['undo_step'].includes(triggerName) ){
                    
                    postURL = '/api/lifecycles/stage/undo';

                }else if( ['reverse_cancel'].includes(triggerName) ){
                    stageData.skip_status = false;
                    stageData.manual_verification_status = false;
                    stageData.cancelled_status = false;
                    stageData.pending_status = false;

                }else if( triggerName ==  'notify_client' ){

                }
                
                if( makeApi && postURL ){

                    var self = this;

                    //  Start loader
                    this.isSaving = true;
                    
                    //  Stage data to save
                    stageData = {
                        modelId: this.modelId,
                        modelType: this.modelType,
                        stage: stageData
                    };

                    console.log('Attempt to handle lifecycle trigger...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', postURL , stageData)
                        .then(({ data }) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isSaving = false;
                            
                            //  Alert creation success
                            self.$Message.success('Lifecycle updated sucessfully!');

                            self.updateLifecycle(data);

                        })         
                        .catch(response => { 
                            //  Stop loader
                            self.isSaving = false;

                            console.log('defaultLifecycle.vue - Error updating lifecycle...');
                            console.log(response);
                        });

                }
            }
        },
    };
    
</script>