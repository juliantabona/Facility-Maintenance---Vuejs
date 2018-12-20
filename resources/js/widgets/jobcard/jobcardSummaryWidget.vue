<template>

    <Card :style="{ width: '100%' }">

        <!-- Jobcard Header -->

            <!-- Jobcard Title -->
            <jobcardHeaderTitle slot="title"></jobcardHeaderTitle>

            <!-- Jobcard Menu -->
            <jobcardHeaderMenu slot="extra" v-bind="$props"></jobcardHeaderMenu>

        <!-- Jobcard Body -->
        
        <jobcardSummaryBody v-bind="$props"></jobcardSummaryBody>

        <!-- Jobcard Footer -->
        
        <jobcardSummaryFooter v-bind="$props"></jobcardSummaryFooter>
        
    </Card>

</template>

<script>

    import jobcardHeaderTitle from './../../components/jobcard/header/title.vue';
    import jobcardHeaderMenu from './../../components/jobcard/header/menu.vue';
    import jobcardSummaryBody from './../../components/jobcard/body/main.vue';
    import jobcardSummaryFooter from './../../components/jobcard/footer/main.vue';

    export default {
        components: { jobcardHeaderTitle, jobcardHeaderMenu, jobcardSummaryBody, jobcardSummaryFooter },
        props: {
            jobcard: {
                type: Object,
                default: () => {}
            },
            /*  Jobcard header related properties  */
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            showAuthourizedStatus: {
                type: Boolean,
                default: true
            },  
            showProcessStatus: {
                type: Boolean,
                default: true
            }, 
             /*  Jobcard body related properties  */
            showTitle: {
                type: Boolean,
                default: true
            }, 
            showDescription: {
                type: Boolean,
                default: true
            }, 
            showDeadline: {
                type: Boolean,
                default: true
            }, 
            showStartDate: {
                type: Boolean,
                default: true
            }, 
            showEndDate: {
                type: Boolean,
                default: true
            }, 
            showPriority: {
                type: Boolean,
                default: true
            }, 
            showCategory: {
                type: Boolean,
                default: true
            }, 
            showCostCenters: {
                type: Boolean,
                default: true
            },
            showCreatedBy: {
                type: Boolean,
                default: true
            }, 
            showCreatedByDate: {
                type: Boolean,
                default: true
            }, 
            showAuthourizedBy: {
                type: Boolean,
                default: true
            }, 
            showAuthourizedByDate: {
                type: Boolean,
                default: true
            },
            showResourceTags: {
                type: Boolean,
                default: true     
            },
            /*  Footer properties    */
            showViewBtn: {
                type: Boolean,
                default: true
            }, 
            showDownloadBtn: {
                type: Boolean,
                default: true
            }, 
            showSendBtn: {
                type: Boolean,
                default: true
            }, 
            showPublicBtn: {
                type: Boolean,
                default: true
            },
        }
    };
  
</script>