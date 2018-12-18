<style scoped>
    .expand-row{
        margin-bottom: 16px;
    }
</style>
<template>

    <Row :gutter="20">

        <Col span="8">
            <companySummaryWidget 
                
                :company="row" :companyId="null" :companyBranchId="row.client_id" type="supplier"

                :showMenuBtn="true"
                :showViewBtn="true" :showEditBtn="true" :showTrashBtn="true" :showAddContactBtn="true"
                :showDownloadProfileBtn="true" :showDownloadLogoBtn="true"
                :showContactsTagBtn="true">
            </companySummaryWidget>
        </Col>

        <Col span="16">
            <jobcardSummaryWidget 

                :jobcard="row"
                
                :showMenuBtn="true" :showMenuEditBtn="true" :showRemoveBtn="true" :showMenuAddClientBtn="true"
                :showMenuAddSupplierBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                :showDescriptionSection="true" :showStatusSection="true" :showPublishSection="true" 
                :showResourceSection="true" :showActionToolbalSection="true">
            </jobcardSummaryWidget>

        </Col>

    </Row>

</template>
<script>
    import companySummaryWidget from './../company/company-summary-widget.vue';
    import jobcardSummaryWidget from './../jobcard/jobcard-summary-widget.vue';
    export default {
        components: { companySummaryWidget , jobcardSummaryWidget },
        props: {
            row: Object,
            jobcard: {
                type: Object,
                default: () => {}
            },
        }
    };
</script>