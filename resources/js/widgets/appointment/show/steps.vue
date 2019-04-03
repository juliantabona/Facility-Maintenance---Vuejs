<template>

    <Row>   
        
        <Col :span="24">
        
            <!-- Get the stage for approving the localCompany -->
            <approvingStage v-bind="$props" :stageNumber="1"
                @toggleEditMode="$emit('toggleEditMode', $event)" @approved="$emit('approved', $event)">
            </approvingStage>
            
        </Col>

    </Row>

</template>
<script type="text/javascript">

    import approvingStage from './../../../components/_common/steps/appointmentApprovingStage.vue';  

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
        components: { approvingStage }
    }
</script>
