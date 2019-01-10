<template>

    <Row :gutter="20">

        <Col span="8">

            <!-- Company summary information -->

            <companySummaryWidget 
                
                v-model="company" :companyId="row.id" :companyBranchId="null" :relationType="relationType"

                :showMenuBtn="true" :showAuthourizedStatus="true" 
                :showLogo="true" :showName="true" :showAddress="true" :showCityOrTown="true"
                :showPhone="true" :showEmail="true" :showContacts="true"
                :showViewBtn="true">
            </companySummaryWidget>

        </Col>

        <Col span="16">

            <!-- Company Jobcards - Displayed as summaries -->

            <companyJobcardSummaryWidget :modelId="row.id" :relationType="relationType"></companyJobcardSummaryWidget>

        </Col>

    </Row>

</template>
<script>
    import companySummaryWidget from './../company/companySummaryWidget.vue';
    import companyJobcardSummaryWidget from './companyJobcardSummaryWidget.vue';
    export default {
        components: { companySummaryWidget , companyJobcardSummaryWidget },
        props: {
            // row: This is the instance of the current table row data. 
            // In this case it represents the company instance
            row: {
                type: Object,
                default: null
            },
            relationType: {
                type: String        //  client, supplier
            }
        },
        data(){
            return {
                company: {}
            }
        }
    };
</script>