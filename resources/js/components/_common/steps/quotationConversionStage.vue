<template>

    <div>

        <!-- Fade loader - Shows when Converting quotation  -->
        <fadeLoader :loading="isConvertingQuotation" msg="Converting, please wait..." class="mt-1 mb-3"></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="3" :showHeader="!localQuotation.has_converted" 
            :disabled="isConvertingQuotation || (!localQuotation.has_sent && !localQuotation.has_skipped_sending)" :showVerticalLine="true"
            :leftWidth="16" :rightWidth="8">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This quotation has not been converted to an invoice. <span v-if="localQuotation.has_sent || localQuotation.has_skipped_sending">Convert now <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span></span>
            
            </template>

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 class="text-secondary">{{ localQuotation.has_converted ? 'Quotation Converted' : 'Convert Quotation' }}</h4>
                <Poptip word-wrap width="200" :trigger="(localQuotation.has_sent || localQuotation.has_skipped_sending) ? 'hover' : 'click'" :content="( (localQuotation.last_converted_activity || {}).created_at) | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span v-if="!localQuotation.has_converted" class="font-weight-bold">Last Converted: Never</span>
                        <span v-if="localQuotation.has_converted" class="font-weight-bold">Last Converted:</span> {{ (localQuotation.last_converted_activity || {}).created_at | moment("from", "now") }}
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <!-- Focus Ripple  -->
                <focusRipple v-if="(localQuotation.has_sent || localQuotation.has_skipped_sending)" 
                            :ripple="!localQuotation.has_converted && (localQuotation.has_sent || localQuotation.has_skipped_sending)" color="blue" class="float-right">

                    <!-- Convert Quotation Button  -->
                    <Button :type="(!localQuotation.has_converted) ? 'primary' : 'default' " size="large" @click="approveQuotation()">
                        <span>{{ (!localQuotation.has_converted) ? 'Convert To Invoice' : 'Convert Again'  }}</span>
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
                isConvertingQuotation: false,
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
                self.isConvertingQuotation = true;

                console.log('Attempt to approve quotation...');
                console.log( self.localQuotation );

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+self.localQuotation.id+'/convert')
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isConvertingQuotation = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Quotation converted sucessfully!');

                        self.$router.push({ name: 'show-invoice', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isConvertingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error Converting quotation...');
                        console.log(response);
                    });
            }
        }
    }
</script>
