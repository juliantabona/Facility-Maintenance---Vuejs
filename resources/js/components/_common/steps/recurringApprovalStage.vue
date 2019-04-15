<template>

    <div>

        <!-- Fade loader - Shows when approving the recurring  -->
        <fadeLoader :loading="isSavingApproval" :msg="'Approving recurring '+resourceName+', please wait...'"></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="4" 
            :showCheckMark="showCheckMark" 
            :showHeader="false" 
            :disabled="disabled" 
            :showVerticalLine="false"
            :isSaving="isSavingApproval" 
            :leftWidth="16" :rightWidth="8">

            <template slot="leftContent">

                <h4 :class="'text-secondary' + (hasApproved ? ' mt-2': '')">
                    {{ hasApproved ? 'Approved': 'Approve:' }}
                </h4>
                <span v-if="!hasApproved" class="d-inline-block mt-2 mb-2">Approve to allow for recurring {{ resourceNamePlural }} to start.</span>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <!-- Focus Ripple  -->
                <focusRipple v-if="showActionBtns && !hasApproved" :ripple="rippleEffect" color="blue" class="float-right mt-3">
                    
                    <!-- Approve Button  -->
                    <Poptip confirm :title="'Are you sure you want to approve this recurring '+resourceName+'?'"  width="300"
                            ok-text="Yes" cancel-text="No" @on-ok="approveRecurringSettings()" placement="left">
                        
                        <Button type="primary" size="large">
                            <span>Approve</span>
                        </Button>

                    </Poptip>

                </focusRipple>

            </template>
            
        </stagingCard>

    </div>

</template>
<script type="text/javascript">

    /*  Steps  */
    import stagingCard from './main.vue';

    /*  Loaders  */
    import fadeLoader from './../loaders/fadeLoader.vue';

    /*  Animated Icons  */
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';

    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    export default {
        components: { fadeLoader, stagingCard, animatedCheckmark, focusRipple },
        props: {
            recurringSettings: {
                type: Object,
                default: null
            },
            resourceName:{
                type: String,
                default: '___'
            },
            resourceNamePlural:{
                type: String,
                default: '___'
            },
            disabled:{
                type: Boolean,
                default: false    
            },
            showCheckMark:{
                type: Boolean,
                default: false    
            },
            hasApproved:{
                type: Boolean,
                default: false    
            },
            showActionBtns:{
                type: Boolean,
                default: false    
            },
            rippleEffect:{
                type: Boolean,
                default: false    
            },
            url:{
                type: String,
                default: null
            }
        },
        data(){
            return {

                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main invoice. This is so that whatever changes we make to 
                    the localRecurringSettings, they must not affect the parent "invoice". We will only update
                    the parent when we save the changes to the database.
                */
                localRecurringSettings: _.cloneDeep( (this.recurringSettings || {}) ),

                isSavingApproval: false
    
            } 
        },
        watch: {

            //  Watch for changes on the recurringSettings
            recurringSettings: {
                handler: function (val, oldVal) {
                    
                    //  Update the local recurringSettings value
                    this.localRecurringSettings = _.cloneDeep(val);

                },
                deep: true
            }
        },
        methods: {
            approveRecurringSettings(){

                var self = this;

                //  Start loader
                self.isSavingApproval = true;

                //  Form data to send
                let RecurringSettingsData = { settings: self.localRecurringSettings };

                if( this.url ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.url, RecurringSettingsData)
                        .then(({ data }) => {
                            
                        console.log(data);

                        //  Stop loader
                        self.isSavingApproval = false;
                        
                        //  Alert creation success
                        self.$Message.success('Approved sucessfully!');

                        self.$emit('saved', data);

                        })         
                        .catch(response => { 
                            //  Stop loader
                            self.isSavingApproval = false;

                            console.log('recurringSettingsStage.vue - Error in approving...');
                            console.log(response);
                        });

                }
            }
        }
    }
</script>
