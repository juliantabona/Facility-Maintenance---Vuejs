<style scoped>

    .el-form-item{
        margin-bottom: 10px !important;
    }

    .el-form-item >>> label{
        width:100% !important;
        text-align: left !important;
        margin:0 !important;
    }

    .el-form-item >>> .el-form-item__content{
        margin-left: 0 !important;
    }

</style>

<template>

    <el-form :model="formData" :rules="rules" ref="jobcardForm" label-width="120px">
        <Row :gutter="20">
            
            <!-- Jobcard create title, description -->
            <Col span="24">
                <jobcardBodyTitle v-bind="$props"></jobcardBodyTitle>
            </Col>
            <Col span="24">
                <jobcardBodyDescription v-bind="$props"></jobcardBodyDescription>
            </Col>

            <!-- Jobcard create start date, end date -->
            <Col span="12">
                <jobcardBodyStartDate v-bind="$props"></jobcardBodyStartDate>
            </Col>
            <Col span="12">
                <jobcardBodyEndDate v-bind="$props"></jobcardBodyEndDate>
            </Col>

            <Col span="24">
                <Divider dashed class="mt-3 mb-3" />
            </Col>

            <!-- Jobcard create category, priority and costcenters -->
            <Col span="12" class="mb-3">
                <jobcardBodyCategory v-bind="$props"></jobcardBodyCategory>
            </Col>
            <Col span="12" class="mb-3">
                <jobcardBodyPriority v-bind="$props"></jobcardBodyPriority>
            </Col>
            <Col span="24">
                <jobcardBodyCostCenter v-bind="$props"></jobcardBodyCostCenter>
            </Col>

            <Col span="24">
                <Divider dashed class="mt-3 mb-3" />
            </Col>    
        </Row>
    </el-form>

</template>
<script>
    import jobcardBodyTitle from './title.vue';
    import jobcardBodyDescription from './description.vue';
    import jobcardBodyStartDate from './startDate.vue';
    import jobcardBodyEndDate from './endDate.vue';
    
    import jobcardBodyPriority from './priority.vue';
    import jobcardBodyCategory from './category.vue';
    import jobcardBodyCostCenter from './costCenter.vue';

    export default {
        props: [
            /*  Form data  */
            'rules',
            'formData'
        ],
        components: { 
            jobcardBodyTitle, jobcardBodyDescription, jobcardBodyStartDate,
            jobcardBodyEndDate, jobcardBodyPriority, jobcardBodyCategory, jobcardBodyCostCenter
        },
        watch: {
            //  When the submittingForm changes in value e.g) true/false
            'formData.submittingForm'() {
                
                //  If the submitting of the form is set to true
                if( this.formData.submittingForm ){
                    
                    //  Then lets validate and submit the jobcard
                    this.validateForm();

                }

            }
        },
        methods: {
            validateForm(){

                this.$refs['jobcardForm'].validate((valid) => {
                    if (valid) {
                        this.submitForm();
                    } else {
                        this.reportValidationFail();
                    }
                })

            },
            reportValidationFail(){

                this.$Message.error('Complete jobcard!');

                this.$emit('validateFail');
                
            },
            submitForm(){

                var self = this;

                //  Start loader
                self.isSubmitting = true;

                console.log('Attempt to create jobcard...');

                //  Form data to send
                let jobcardData = {
                    jobcard_title: this.formData.title,
                    jobcard_description: this.formData.description,
                    jobcard_start_date: this.formData.startDate,
                    jobcard_end_date: this.formData.endDate,
                    jobcard_priority: this.formData.priority.id,
                    jobcard_categories: this.formData.categories.map(category => category.id),
                    jobcard_costcenters: this.formData.costCenters.map(costCenter => costCenter.id)
                };

                console.log(jobcardData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/jobcards', jobcardData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSubmitting = false;
                        
                        //  Get the created jobcard
                        let jobcardId = data.id;

                        console.log('Go to jobcard...');

                        //  Alert creation success
                        this.$Message.success('Jobcard created!');

                        //  Navigate to the jobcard
                        self.$router.push({ name: 'show-jobcard', params:{ id: jobcardId } });

                    })         
                    .catch(response => { 
                        console.log('jobcardCreateWidget.vue - Error creating jobcard...');
                        console.log(response);
                    });
            }
        }
    };
</script>