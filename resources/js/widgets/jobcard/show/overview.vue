<template>

    <Row class="border-bottom mb-3 pb-3">

        <!-- Jobcard Reference Number  -->
        <Col span="5">
            <h2 class="text-dark">Jobcard #{{ localJobcard.id }}</h2>
        </Col>

        <!-- Jobcard Status  -->
        <Col span="3">
            <h6 class="text-secondary">Status</h6>
            <h5><JobcardStatusTag :jobcard="jobcard"></JobcardStatusTag></h5>   
        </Col>

        <!-- Jobcard Priority  -->
        <Col span="3">
            <h6 class="text-secondary">Priority</h6>
            <priorityTag :priority="localJobcard.priority"></priorityTag>
        </Col>

        <!-- Jobcard Lifecycle  -->
        <Col span="4">
            <h6 class="text-secondary">Lifecycle</h6>
            <JobcardLifecycleStatusTag :jobcard="jobcard"></JobcardLifecycleStatusTag>
        </Col>

        <!-- Jobcard Type e.g) Private, Goverment, Parastatal  -->
        <Col span="4">
            <h6 class="text-secondary">Deadline</h6>     
            <h5>{{ localJobcard.end_date | moment("from", "now")  }}</h5>         
        </Col>

        <!-- Jobcard Due Date  -->
        <Col span="4">
            <h6 class="text-secondary">Created Date</h6>
            <h5>{{ localJobcard.created_at | moment("from", "now")  }}</h5>            
        </Col>

        <!-- Jobcard Menu -->
        <Col span="1">
            <menuToggle :jobcardId="localJobcard.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    /*  Statuses   */
    import JobcardStatusTag from './../../../components/_common/statuses/JobcardStatusTag.vue';  
    import JobcardLifecycleStatusTag from './../../../components/_common/statuses/JobcardLifecycleStatusTag.vue';  

    /*  Tags   */
    import priorityTag from './../../../components/_common/tags/priorityTag.vue'; 

    /*  Menu Dropdowns   */
    import menuToggle from './../../../components/_common/dropdowns/jobcardMenuDropdown.vue';

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            jobcard: {
                type: Object,
                default: null
            }
        },
        components: { JobcardStatusTag, JobcardLifecycleStatusTag, priorityTag, menuToggle },
        data() {
            return {
                localJobcard: this.jobcard,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the jobcard
            jobcard: {
                handler: function (val, oldVal) {

                    //  Update the local jobcard value
                    this.localJobcard = val;

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    //  Update the edit mode value
                    this.localEditMode = val;
                }
            }
        },
        computed:{
            customerName: function(){
                if(this.localJobcard.client.model_type == 'user'){
                    return this.localJobcard.client.full_name;
                }else if(this.localJobcard.client.model_type == 'company'){
                    return this.localJobcard.client.name;
                }
            }
        },
        methods: {
            
        },
        created () {
            
        }
    }
</script>
