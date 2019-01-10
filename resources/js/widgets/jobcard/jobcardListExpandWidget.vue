<template>

    <Row :gutter="20">

        <Col span="8">

            <!-- Get the company summary details -->
            
            <companySummaryWidget 
                
                v-model="company" :companyId="null" :companyBranchId="row.client_id" relationType="client"

                :showMenuBtn="true" :showAuthourizedStatus="true" 
                :showLogo="true" :showName="true" :showAddress="true" :showCityOrTown="true"
                :showPhone="true" :showEmail="true" :showContacts="true"
                :showViewBtn="true">
            </companySummaryWidget>

        </Col>

        <Col span="16">
            
            <!-- Get the jobcard summary details -->

            <jobcardSummaryWidget 

                v-model="jobcard" :jobcardId="row.id"
                
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
    import jobcardSummaryWidget from './jobcardSummaryWidget.vue';
    export default {
        components: { companySummaryWidget , jobcardSummaryWidget },
        props: {
            // row: This is the instance of the current table row data. 
            // In this case it represents the jobcard instance
            row: {
                type: Object,
                default: null
            },
        },
        data(){
            return {
                jobcard: {},
                company: {}
            }
        }
    };
</script>