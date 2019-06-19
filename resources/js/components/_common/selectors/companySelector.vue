<template>

    <!-- Company Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>
        <Select v-if="localfetchedCompanies.length" 
                v-model="localSelectedCompany" 
                placeholder="Select company" 
                not-found-text="No companies found" 
                filterable>
            <Option 
                v-for="company in localfetchedCompanies" 
                :value="JSON.stringify(company)" 
                :key="company.id">{{ company.name }}
            </Option>
        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedCompany:{
                type: Object,
                default: null
            }
        },
        components: { Loader },
        data(){
            return {
                localfetchedCompanies: [],
                isLoading: false,
            }
        },
        computed:{
            localSelectedCompany:{
                get(){
                    return JSON.stringify(this.selectedCompany);
                },
                set(val){
                    if(val != ''){
                        var updatedCompany = JSON.parse(val);
                        this.$emit('updated:company',  updatedCompany );
                    }

                }
            }
        },
        methods: {
            fetch() {

                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting companies...');

                //  Settings to prevent pagination
                var pagination = '&paginate=0';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies?allocation=company&type=client,supplier'+pagination)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get currencies
                        self.localfetchedCompanies = data;

                        console.log('New fetched companies');

                        console.log(self.localfetchedCompanies);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('companySelector.vue - Error getting companies...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>