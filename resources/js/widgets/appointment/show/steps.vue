<template>

    <Row>   
        
        <Col :span="24">
        
            <!-- Get the stage for approving the appointment -->
            <approvingStage v-bind="$props" :stageNumber="1"
                @toggleEditMode="$emit('toggleEditMode', $event)" 
                @approved="$emit('approved', $event)">
            </approvingStage>
            
            <!-- Get the stage for sending the appointment -->
            <sendingStage v-bind="$props" 
                @sent="$emit('sent', $event)" 
                @skipped="$emit('skipped', $event)">
            </sendingStage>
            
            <!-- Get the stage for the appointment confirmation, setting confirmation reminders and cancelling confirmations -->
            <confirmationStage v-bind="$props" 
                @confirmed="$emit('confirmed', $event)" 
                @cancelled="$emit('cancelled', $event)" 
                @reminderSet="$emit('reminderSet', $event)">
            </confirmationStage>
        </Col>

    </Row>

</template>
<script type="text/javascript">

    import approvingStage from './../../../components/_common/steps/appointmentApprovingStage.vue';  
    import sendingStage from './../../../components/_common/steps/appointmentSendingStage.vue';
    import confirmationStage from './../../../components/_common/steps/appointmentConfirmationStage.vue';

    export default {
        props: {
            createMode: {
                type: Boolean,
                default: false
            },
            editMode: {
                type: Boolean,
                default: false
            },
            appointment: {
                type: Object,
                default: null
            }
        },
        components: { approvingStage, sendingStage, confirmationStage }
    }
</script>
