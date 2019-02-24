<template>

    <Row>   
        
        <Col :span="24">
            
            <!-- Get the stage for setting the recurring schedule plan -->
            <invoiceRecurringSchedulePlanStage v-bind="$props" :style="{ position:'relative', zIndex: 3 }" 
                @approved="$emit('approved', $event)">
            </invoiceRecurringSchedulePlanStage>

            <!-- Get the stage for setting the recurring payment plan -->
            <invoiceRecurringPaymentPlanStage v-bind="$props" :style="{ position:'relative', zIndex: 2 }" 
                @approved="$emit('approved', $event)">
            </invoiceRecurringPaymentPlanStage>

            <!-- Get the stage for setting the recurring delivery plan -->
            <invoiceRecurringDeliveryPlanStage v-bind="$props" :style="{ position:'relative', zIndex: 1 }" 
                @approved="$emit('approved', $event)">
            </invoiceRecurringDeliveryPlanStage>

        </Col>

    </Row>

</template>
<script type="text/javascript">

    import invoiceRecurringSchedulePlanStage from './../../../components/_common/steps/invoiceRecurringSchedulePlanStage.vue'; 
    import invoiceRecurringPaymentPlanStage from './../../../components/_common/steps/invoiceRecurringPaymentPlanStage.vue'; 
    import invoiceRecurringDeliveryPlanStage from './../../../components/_common/steps/invoiceRecurringDeliveryPlanStage.vue'; 

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        components: { invoiceRecurringSchedulePlanStage, invoiceRecurringPaymentPlanStage, invoiceRecurringDeliveryPlanStage }
    }
</script>
