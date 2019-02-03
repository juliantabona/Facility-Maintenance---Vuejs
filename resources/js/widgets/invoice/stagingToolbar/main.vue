<template>

    <Row>   
        
        <Col :span="24">
            
            <!-- Get the stage for approving the invoice -->
            <approvingStage v-bind="$props" 
                @toggleEditMode="$emit('toggleEditMode', $event)" @approved="$emit('approved', $event)">
            </approvingStage>
            
            <!-- Get the stage for sending the invoice -->
            <sendingStage v-bind="$props" @sent="$emit('sent', $event)"></sendingStage>
            
            <!-- Get the stage for sending the invoice -->
            <paymentStage v-bind="$props" 
                @paid="$emit('paid', $event)" @cancelled="$emit('cancelled', $event)" @reminderSet="$emit('reminderSet', $event)">
            </paymentStage>
            
        </Col>

    </Row>

</template>
<script type="text/javascript">

    import approvingStage from './approvingStage.vue';  
    import sendingStage from './sendingStage.vue';
    import paymentStage from './paymentStage.vue';

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
