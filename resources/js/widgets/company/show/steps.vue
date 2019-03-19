<template>

    <Row>   
        
        <Col :span="24">
        
            <!-- Get the stage for approving the localCompany -->
            <approvingStage v-bind="$props" 
                @toggleEditMode="$emit('toggleEditMode', $event)" @approved="$emit('approved', $event)">
            </approvingStage>
            
        </Col>

    </Row>

</template>
<script type="text/javascript">

    import approvingStage from './../../../components/_common/steps/companyApprovingStage.vue';  

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
            company: {
                type: Object,
                default: null
            }
        },
        components: { approvingStage }
    }
</script>
