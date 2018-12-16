<style scoped>
    .expand-row{
        margin-bottom: 16px;
    }
</style>
<template>

    <Row :gutter="20">

        <Col span="8">
            <companySummaryWidget :company="company" type="contractor"
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
                :showMenuAddContractorBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
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
        },
        data(){
            return {
                company: {
                    logo_url: 'http://acmelogos.com/images/logo-8.svg',
                    name: 'Optimum Quality (Pty) Ltd',
                    city: 'Gaborone',
                    address: 'Plot 2356, Fairgrounds',
                    phone_ext: '267',
                    phone_num: '3909083',
                    email: 'info@optimumqbw.com'
                }
            }
        }
    };
</script>