<template>

    <div>
        
        <!-- Fade loader - Shows when approving company  -->
        <fadeLoader :loading="isApprovingCompany" msg="Approving, please wait..." class="mt-1 mb-3"></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showHeader="!localCompany.has_approved" 
            :disabled="isApprovingCompany" :showVerticalLine="true"
            :leftWidth="16" :rightWidth="8">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This is a DRAFT company. You can take further actions once you approve it. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
            
            </template>

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 v-if="" class="text-secondary">{{ localCompany.has_approved ? 'Company Approved' : 'Approve Company' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="localCompany.created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span class="font-weight-bold">Created:</span> {{ localCompany.created_at | moment("from", "now") | capitalize }}</a>
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <Button class="float-right ml-2" type="default" size="large" @click.native="$emit('toggleEditMode', true)">
                    <span>{{ localCompany.has_approved ? 'Edit Company' : 'Edit Draft' }}</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple v-if="!localCompany.has_approved" color="blue" :ripple="true" class="float-right">

                    <!-- Create Company Button  -->
                    <Button type="primary" size="large" @click="approveCompany()">
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
            company: {
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
                isApprovingCompany: false,
                localCompany: this.company,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the company
            company: {
                handler: function (val, oldVal) {

                    //  Update the local company value
                    this.localCompany = val;

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
            
            approveCompany(){

                var self = this;

                //  Start loader
                self.isApprovingCompany = true;

                console.log('Attempt to approve company...');
                console.log( self.localCompany );

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies/'+self.localCompany.id+'/approve')
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isApprovingCompany = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Company approved sucessfully!');

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isApprovingCompany = false;

                        console.log('companySummaryWidget.vue - Error approving company...');
                        console.log(response);
                    });
            }
        }
    }
</script>
