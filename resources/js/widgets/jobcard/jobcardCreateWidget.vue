<template>
    <div>

        <Card :style="{ width: '100%' }">

            <!-- Create Jobcard Header -->

                <!-- Create Jobcard Title -->
                <jobcardHeaderTitle slot="title"></jobcardHeaderTitle>

            <!-- Create Jobcard Body -->
            <jobcardSummaryBody v-bind="$props" @validateFail="formData.submittingForm = false"></jobcardSummaryBody>

            <!-- Create Jobcard Footer -->
            <jobcardSummaryFooter v-bind="$props" @runCreate="formData.submittingForm = true"></jobcardSummaryFooter>
            
        </Card>

    </div>

</template>

<script>

    import jobcardHeaderTitle from './../../components/jobcard/create/header/title.vue';
    import jobcardSummaryBody from './../../components/jobcard/create/body/main.vue';
    import jobcardSummaryFooter from './../../components/jobcard/create/footer/main.vue';

    export default {
        components: { jobcardHeaderTitle, jobcardSummaryBody, jobcardSummaryFooter },
        props: {
            formData: {
                default(){
                    return {
                        title: '',
                        description: '',
                        startDate: '',
                        endDate: '',
                        priority: null,
                        categories: [],
                        costCenters: [],
                        submittingForm: false,
                    }
                }
            },
            rules: {
                default(){
                    return {
                        title: [
                            { required: true, message: 'Include the jobcard title', trigger: 'blur' }
                        ],
                        startDate: [
                            { required: true, message: 'Include the jobcard start date', trigger: 'blur' }
                        ],
                        endDate: [
                            { required: true, message: 'Include the jobcard end date', trigger: 'blur' }
                        ],
                        priority: [
                            { required: true, message: 'Select a priority for this jobcard', trigger: 'blur' }
                        ],
                        categories: [
                            { required: true, message: 'Select a category for this jobcard', trigger: 'blur' }
                        ]
                    }
                }
            },
            priorityOptions: [],
            categoryOptions: [],
            costCenterOptions: [],
        },
        data(){
            return {
                
            }
        }
    };
  
</script>