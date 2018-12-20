<template>

    <Row :gutter="20">

        <Col span="8">
            <!-- Get the company summary details -->
            <companySummaryWidget 
                
                :company="null" :companyId="null" :companyBranchId="row.client_id" type="client"
                
                :showMenuBtn="true"
                :showViewBtn="true" :showEditBtn="true" :showTrashBtn="true" :showAddContactBtn="true"
                :showDownloadProfileBtn="true" :showDownloadLogoBtn="true"
                :showContactsTagBtn="true">
            </companySummaryWidget>
        </Col>

        <Col span="16">
            <!-- Get the jobcard summary details -->
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
    import jobcardSummaryWidget from './jobcardSummaryWidget.vue';
    export default {
        components: { companySummaryWidget , jobcardSummaryWidget },
        props: {
            // row: This is the instance of the current table row data. 
            // In this case it represents the jobcard instance
            row: {
                type: Object,
                default: () => {}
            },
        }
    };
</script>