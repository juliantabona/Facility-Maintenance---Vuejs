<template>

    <Row>   
        
        <Col :span="24">
            
            <!-- Get the stage for approving the quotation -->
            <quotationApprovingStage v-bind="$props" 
                @toggleEditMode="$emit('toggleEditMode', $event)" @approved="$emit('approved', $event)">
            </quotationApprovingStage>
            
            <!-- Get the stage for sending the quotation -->
            <quotationSendingStage v-bind="$props" @sent="$emit('sent', $event)" @skipped="$emit('skipped', $event)"></quotationSendingStage>
            
            <!-- Get the stage for recording the quotation payment, setting payment reminders and sending receipts -->
            <quotationConversionStage v-bind="$props" 
                @converted="$emit('converted', $event)">
            </quotationConversionStage>
            
        </Col>

    </Row>

</template>
<script type="text/javascript">

    import quotationApprovingStage from './../../../components/_common/steps/quotationApprovingStage.vue';  
    import quotationSendingStage from './../../../components/_common/steps/quotationSendingStage.vue';
    import quotationConversionStage from './../../../components/_common/steps/quotationConversionStage.vue';

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
            quotation: {
                type: Object,
                default: null
            }
        },
        components: { quotationApprovingStage, quotationSendingStage, quotationConversionStage }
    }
</script>
