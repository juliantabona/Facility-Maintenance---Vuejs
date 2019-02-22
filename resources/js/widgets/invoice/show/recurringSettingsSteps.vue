<template>

    <Row>   
        
        <Col :span="24">
            
            <!-- Get the stage for setting the recurring schedule -->
            <invoiceRecurringSchedulePlanStage v-bind="$props" 
                @approved="$emit('approved', $event)">
            </invoiceRecurringSchedulePlanStage>
            
            <!-- Get the stage for setting the recurring schedule -->
            <invoiceRecurringDeliveryPlanStage v-bind="$props" 
                @approved="$emit('approved', $event)">
            </invoiceRecurringDeliveryPlanStage>

        </Col>

    </Row>

</template>
<script type="text/javascript">

    import invoiceRecurringSchedulePlanStage from './../../../components/_common/steps/invoiceRecurringSchedulePlanStage.vue'; 
    import invoiceRecurringDeliveryPlanStage from './../../../components/_common/steps/invoiceRecurringDeliveryPlanStage.vue'; 

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        components: { invoiceRecurringSchedulePlanStage, invoiceRecurringDeliveryPlanStage }
    }
</script>
