<style scoped>
    .expand-row{
        margin-bottom: 16px;
    }
</style>
<template>

    <Row :gutter="20">

        <Col span="8">

            <!-- Company summary information -->

            <companySummaryWidget 
                
                v-model="row" :companyId="null" :companyBranchId="null" relationType="supplier"

                :showMenuBtn="true" :showAuthourizedStatus="true" 
                :showLogo="true" :showName="true" :showAddress="true" :showCityOrTown="true"
                :showPhone="true" :showEmail="true" :showContacts="true"
                :showViewBtn="true">
            </companySummaryWidget>

        </Col>

        <Col span="16">

            <!-- Get the jobcard summary details -->

            <jobcardSummaryWidget 

                v-model="row" :jobcardId="null"
                
                :showMenuBtn="true" :showAuthourizedStatus="true" :showProcessStatus="true"
                :showTitle="true" :showDescription="true" :showDeadline="true" :showStartDate="true" :showEndDate="true"
                :showPriority="true" :showCategory="true" :showCostCenters="true" :showCreatedBy="true" :showCreatedByDate="true"
                :showAuthourizedBy="true" :showAuthourizedByDate="true" :showResourceTags="false" 
                :showViewBtn="true" :showDownloadBtn="true" :showSendBtn="true" :showPublicBtn="true">
            </jobcardSummaryWidget>


        </Col>

    </Row>

</template>
<script>
    import companySummaryWidget from './../company/companySummaryWidget.vue';
    import jobcardSummaryWidget from './../jobcard/jobcardSummaryWidget.vue';
    export default {
        components: { companySummaryWidget , jobcardSummaryWidget },
        props: {
            row: Object,
            jobcard: {
                type: Object,
                default: null
            },
        }
    };
</script>