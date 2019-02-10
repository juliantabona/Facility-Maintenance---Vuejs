<template>

    <Row>   
        
        <Col :span="24">
            
            <!-- Get the stage for approving the invoice -->
            <approvingStage v-bind="$props" 
                @toggleEditMode="$emit('toggleEditMode', $event)" @approved="$emit('approved', $event)">
            </approvingStage>
            
            <!-- Get the stage for sending the invoice -->
            <sendingStage v-bind="$props" @sent="$emit('sent', $event)"></sendingStage>
            
            <!-- Get the stage for recording the invoice payment, setting payment reminders and sending receipts -->
            <paymentStage v-bind="$props" 
                @paid="$emit('paid', $event)" @cancelled="$emit('cancelled', $event)" @reminderSet="$emit('reminderSet', $event)"
                @sent="$emit('sent', $event)">
            </paymentStage>
            
        </Col>

    </Row>

</template>
<script type="text/javascript">

    import approvingStage from './../../../components/_common/steps/approvingStage.vue';  
    import sendingStage from './../../../components/_common/steps/sendingStage.vue';
    import paymentStage from './../../../components/_common/steps/paymentStage.vue';

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
            invoice: {
                type: Object,
                default: null
            }
        },
        components: { approvingStage, sendingStage, paymentStage }
    }
</script>
