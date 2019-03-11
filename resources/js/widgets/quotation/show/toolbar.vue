<template>

    <div>
        
        <div class="float-right mr-2">
            <ColorPicker v-if="secondaryColor" v-model="secondaryColor" @on-change="updateSecondaryColor" class="float-right" recommend alpha />
            <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Secondary Color:</span>
        </div>
        
        <div class="float-right mr-2">
            <ColorPicker v-if="primaryColor" v-model="primaryColor" @on-change="updatePrimaryColor" class="float-right" okText="Ok" format="hex" recommend alpha />
            <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Primary Color:</span>
        </div>
        
        <div class="float-right mr-3">
            <Loader v-if="isLoadingCurrencies" :loading="isLoadingCurrencies" type="text">Loading currencies...</Loader>
            
            <div v-if="fetchedCurrencies.length">
                <currencySelector class="float-right" :style="{maxWidth: '150px'}"
                    :fetchedCurrencies="fetchedCurrencies" :selectedCurrency="localQuotation.currency_type"
                    @updated="updateCurrencyChanges($event)">
                </currencySelector>
                <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Currency:</span>
            </div>
        </div>

    </div>

</template>


<script type="text/javascript">

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';

    /*  Selectors  */
    import currencySelector from './../../../components/_common/selectors/currencySelector.vue';

    export default {
        props: {
            quotation: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
            createMode: {
                type: Boolean,
                default: false
            },
        },
        components: { Loader, currencySelector },
        data(){
            return {
                localQuotation: this.quotation,
                localEditMode: this.editMode,
                primaryColor: (this.quotation.colors || {})[0],
                secondaryColor: (this.quotation.colors || {})[1],
                
                isLoadingCurrencies: false,
                fetchedCurrencies: [],
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {
                    
                    //  Update the local quotation value
                    this.localQuotation = val;

                    //  Update the primary color shortcut
                    this.primaryColor = (val.colors || {})[0];

                    //  Update the secondary color shortcut
                    this.secondaryColor = (val.colors || {})[1];

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;

                }
            },

            //  Watch for changes on the create mode value
            createMode: {
                handler: function (val, oldVal) {

                    //  Update the create mode value
                    this.localCreateMode = val;

                }
            }
        },
        methods: {
            fetchCurrencies() {
                const self = this;

                //  Start loader
                self.isLoadingCurrencies = true;

                console.log('Start getting currencies...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/currencies')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCurrencies = false;

                        //  Get currencies
                        self.fetchedCurrencies = data;
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCurrencies = false;

                        console.log('quotationSummaryWidget.vue - Error getting currencies...');
                        console.log(response);    
                    });
            },
            updateCurrencyChanges(newCurrency){
                this.localQuotation.currency_type = newCurrency;

                this.$Notice.success({
                    title: 'Currency changed to ' + newCurrency.country + ' (' + newCurrency.currency.iso.code + ')'
                });
            },
            updatePrimaryColor(newColor){
                
                /*  We need to use the Vue.set(object, key, value) instead of  this.quotation.colors[0] = newColor, 
                 *  simply because the value of "this.quotation.colors" will change but the changes will not be
                 *  realized by vue since we change a nested and non-reactive property, unless we set that 
                 *  non-reactive property as a v-model proprty e.g) <Tag v-model="quotation.colors[0]"> 
                 *  which would also work. However in this case we will use this.$set() 
                 */ 
                this.$set(this.localQuotation.colors, 0, newColor);
            },
            updateSecondaryColor(newColor){
                
                /*  We need to use the Vue.set(object, key, value) instead of  this.quotation.colors[0] = newColor, 
                 *  simply because the value of "this.quotation.colors" will change but the changes will not be
                 *  realized by vue since we change a nested and non-reactive property, unless we set that 
                 *  non-reactive property as a v-model proprty e.g) <Tag v-model="quotation.colors[0]"> 
                 *  which would also work. However in this case we will use this.$set() 
                 */ 
                this.$set(this.localQuotation.colors, 1, newColor);
            }
        },
        created(){
            this.fetchCurrencies();
        }
    }

</script>
