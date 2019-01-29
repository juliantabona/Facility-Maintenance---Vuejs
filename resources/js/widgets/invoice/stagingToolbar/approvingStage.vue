<template>

    <div>

        <!-- Fade loader - Shows when approving invoice  -->
        <fadeLoader :loading="isApprovingInvoice" msg="Approving, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showHeader="!localInvoice.has_approved" 
            :disabled="isApprovingInvoice" :showVerticalLine="true">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This is a DRAFT invoice. You can take further actions once you approve it. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
            
            </template>

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 v-if="" class="text-secondary">{{ localInvoice.has_approved ? 'Invoice Approved' : 'Approve Invoice' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="localInvoice.created_date_value | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span class="font-weight-bold">Created:</span> {{ localInvoice.created_at | moment("from", "now") | capitalize }} from <a href="#"><span class="font-weight-bold">Estimate #87</span></a>
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <Button class="float-right ml-2" type="default" size="large" @click.native="$emit('toggleEditMode', true)">
                    <span>Edit Draft</span>
                </Button>

                <Button v-if="!localInvoice.has_approved" class="float-right" type="primary" size="large" @click="approveInvoice()">
                    <span>Approve Draft</span>
                </Button>

            </template>
            
        </stagingCard>

    </div>

</template>
<script type="text/javascript">

    import fadeLoader from './fadeLoader.vue';
    import stagingCard from './stagingCard.vue';

    export default {
        components: { fadeLoader, stagingCard },
        props: {
            invoice: {
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
                isApprovingInvoice: false,
                localInvoice: this.invoice,
                locallocalEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    if(this.localInvoice != val){

                        //  Update the local invoice value
                        this.localInvoice = val;
                    }
                }
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    if(this.locallocalEditMode != val){

                        //  Update the edit mode value
                        this.locallocalEditMode = val;
                    }
                }
            }
        },
        methods: {
            
            approveInvoice(){

                var self = this;

                //  Start loader
                self.isApprovingInvoice = true;

                console.log('Attempt to approve invoice...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/approve')
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isApprovingInvoice = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Invoice approved sucessfully!');

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isApprovingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error approving invoice...');
                        console.log(response);
                    });
            }
        }
    }
</script>
