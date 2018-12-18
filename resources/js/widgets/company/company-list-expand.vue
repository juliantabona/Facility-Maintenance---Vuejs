<template>

    <Row :gutter="20">

        <Col span="8">
            <companySummaryWidget 
                
                :company="row" :companyId="null" :companyBranchId="null" :type="type"

                :showMenuBtn="true"
                :showViewBtn="true" :showEditBtn="true" :showTrashBtn="true" :showAddContactBtn="true"
                :showDownloadProfileBtn="true" :showDownloadLogoBtn="true"
                :showContactsTagBtn="true">
            </companySummaryWidget>
        </Col>

        <Col span="16">
            <companyJobcardSummaryWidget 
                :modelId="row.id" :modelType="type">
            </companyJobcardSummaryWidget>
        </Col>

    </Row>

</template>
<script>
    import companySummaryWidget from './../company/company-summary-widget.vue';
    import companyJobcardSummaryWidget from './company-jobcard-summary-widget.vue';
    export default {
        components: { companySummaryWidget , companyJobcardSummaryWidget },
        props: {
            row: Object,
            modelType: {
                type: String,
                default: 'branch'   //  branch, company
            },
            type: {
                type: String,
                default: 'client'   //  client, supplier
            }
        },
    };
</script>