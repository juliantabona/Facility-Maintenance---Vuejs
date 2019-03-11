<template>

    <div>
        
        <!-- Fade loader - Shows when approving quotation  -->
        <fadeLoader :loading="isApprovingQuotation" msg="Approving, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showHeader="!localQuotation.has_approved" 
            :disabled="isApprovingQuotation" :showVerticalLine="true"
            :leftWidth="16" :rightWidth="8">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This is a DRAFT quotation. You can take further actions once you approve it. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
            
            </template>

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 v-if="" class="text-secondary">{{ localQuotation.has_approved ? 'Quotation Approved' : 'Approve Quotation' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="localQuotation.created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span class="font-weight-bold">Created:</span> {{ localQuotation.created_at | moment("from", "now") | capitalize }}</a>
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <Button class="float-right ml-2" type="default" size="large" @click.native="$emit('toggleEditMode', true)">
                    <span>Edit Draft</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple v-if="!localQuotation.has_approved" color="blue" :ripple="true" class="float-right">

                    <!-- Create Quotation Button  -->
                    <Button type="primary" size="large" @click="approveQuotation()">
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
            quotation: {
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
                isApprovingQuotation: false,
                localQuotation: this.quotation,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {

                    //  Update the local quotation value
                    this.localQuotation = val;

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
            
            approveQuotation(){

                var self = this;

                //  Start loader
                self.isApprovingQuotation = true;

                console.log('Attempt to approve quotation...');
                console.log( self.localQuotation );

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+self.localQuotation.id+'/approve')
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isApprovingQuotation = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Quotation approved sucessfully!');

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isApprovingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error approving quotation...');
                        console.log(response);
                    });
            }
        }
    }
</script>
