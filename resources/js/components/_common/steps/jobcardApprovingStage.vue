<template>

    <div>
        
        <!-- Fade loader - Shows when approving jobcard  -->
        <fadeLoader :loading="isApprovingJobcard" msg="Approving, please wait..." class="mt-1 mb-3"></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showHeader="!localJobcard.has_approved" 
            :disabled="isApprovingJobcard" :showVerticalLine="true"
            :leftWidth="16" :rightWidth="8">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This is a DRAFT jobcard. You can take further actions once you approve it. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
            
            </template>

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 v-if="" class="text-secondary">{{ localJobcard.has_approved ? 'Jobcard Approved' : 'Approve Jobcard' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="localJobcard.created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span class="font-weight-bold">Created:</span> {{ localJobcard.created_at | moment("from", "now") | capitalize }}</a>
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <Button class="float-right ml-2" type="default" size="large" @click.native="$emit('toggleEditMode', true)">
                    <span>{{ localJobcard.has_approved ? 'Edit Jobcard' : 'Edit Draft' }}</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple v-if="!localJobcard.has_approved" color="blue" :ripple="true" class="float-right">

                    <!-- Create Jobcard Button  -->
                    <Button type="primary" size="large" @click="approveJobcard()">
                        <span>Approve Draft</span>
                    </Button>

                </focusRipple>

            </template>
            
        </stagingCard>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    export default {
        components: { fadeLoader, stagingCard, focusRipple },
        props: {
            jobcard: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                isApprovingJobcard: false,
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
        methods: {
            
            approveJobcard(){

                var self = this;

                //  Start loader
                self.isApprovingJobcard = true;

                console.log('Attempt to approve jobcard...');
                console.log( self.localJobcard );

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=lifecycle,priority,categories,costcenters,assignedStaff';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/jobcards/'+self.localJobcard.id+'/approve'+connections)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isApprovingJobcard = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Jobcard approved sucessfully!');

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isApprovingJobcard = false;

                        console.log('jobcardSummaryWidget.vue - Error approving jobcard...');
                        console.log(response);
                    });
            }
        }
    }
</script>
