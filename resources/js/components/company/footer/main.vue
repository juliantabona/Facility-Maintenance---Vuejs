<template>

    <Row>

        <Col span="24">

            <!-- Company view button -->

            <companyViewButton v-if="showViewBtn" :route="{ name: 'show-company', params: { id: company.id } }" size="small" class="float-right mr-1"></companyViewButton>


        </Col>
    </Row>

</template>
<script>
    import companyViewButton from './../../_common/button/view.vue';

    export default {
        props: [
            /*  Company data  */
            'company',
            /*  Company footer related properties  */
            'showViewBtn'
        ],
        components: { 
            companyViewButton
        }
    };
</script>