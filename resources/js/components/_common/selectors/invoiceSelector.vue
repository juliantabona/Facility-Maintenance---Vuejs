<template>

    <!-- Invoice Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading invoices...</Loader>
        <Select v-if="localfetchedInvoices.length" 
                v-model="localSelectedInvoice" 
                placeholder="Select invoice" 
                not-found-text="No invoices found" 
                filterable>
            <Option 
                v-for="invoice in localfetchedInvoices" 
                :value="JSON.stringify(invoice)" 
                :key="invoice.id">Invoice #{{ invoice.id }}</Option>
        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedInvoice:{
                type: Object,
                default: null
            },
            selectedInvoiceId:{
                type: Number,
                default: null
            },
            modelType: {
                type: String,
                default: ''   
            }
        },
        components: { Loader },
        data(){
            return {
                localfetchedInvoices: [],
                isLoading: false,
            }
        },
        computed:{
            localSelectedInvoice:{
                get(){

                    var invoice;

                    if( (this.selectedInvoice || {}).length ){
                        
                        for(var x=0; x < this.localfetchedInvoices.length; x++){
                            if(  this.localfetchedInvoices[x]['id'] == this.selectedInvoice['id'] ){
                                invoice = JSON.stringify(this.localfetchedInvoices[x]);
                            }  
                        }
                        
                        return invoice;

                    }else if( this.selectedInvoiceId ){

                        for(var x=0; x < this.localfetchedInvoices.length; x++){
                            if(  this.localfetchedInvoices[x]['id'] == this.selectedInvoiceId ){
                                invoice = JSON.stringify(this.localfetchedInvoices[x]);
                            }  
                        }
                        
                        return invoice;

                    }
                },
                set(val){
                    if(val){
                        var updatedInvoice = JSON.parse(val);
                        this.$emit('updated:invoice',  updatedInvoice );
                    }

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting invoices...');

                //  Additional data to eager load along with each user found
                var connections = '';

                //  Settings to prevent pagination
                var pagination = (connections ? '&': '') + 'paginate=0';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/invoices?'+connections+pagination)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get invoices
                        self.localfetchedInvoices = data;

                        console.log('New fetched invoices');

                        console.log(self.localfetchedInvoices);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('invoiceSelector.vue - Error getting invoices...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>