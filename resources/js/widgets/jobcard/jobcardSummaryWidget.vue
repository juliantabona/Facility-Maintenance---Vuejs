<template>
    <div>
        
        <!-- Jobcard Lifecycle - Only show if jobcard is authourized -->
        <statusLifecycle v-if="JobcardExists && jobcard.authourizedBy" v-bind="$props" class="mb-1"></statusLifecycle>

        <!-- Jobcard Not Authourized Alert -->
        <notAuthourizedAlert v-if="JobcardExists && !jobcard.authourizedBy" v-bind="$props"></notAuthourizedAlert>

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
    </div>

</template>

<script>

    import statusLifecycle from './../../components/jobcard/status/lifecycle.vue';
    import notAuthourizedAlert from './../../components/jobcard/status/notAuthourizedAlert.vue';
    import jobcardHeaderTitle from './../../components/jobcard/header/title.vue';
    import jobcardHeaderMenu from './../../components/jobcard/header/menu.vue';
    import jobcardSummaryBody from './../../components/jobcard/body/main.vue';
    import jobcardSummaryFooter from './../../components/jobcard/footer/main.vue';

    export default {
        components: { statusLifecycle, notAuthourizedAlert, jobcardHeaderTitle, jobcardHeaderMenu, jobcardSummaryBody, jobcardSummaryFooter },
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
        },
        computed: {
            JobcardExists: function(){
                return Object.keys(this.jobcard).length;
            }
        }
    };
  
</script>