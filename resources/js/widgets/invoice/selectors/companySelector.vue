<template>
    
    <div>
        <!-- Company Selector -->
        <Select v-if="localfetchedCompanies.length" v-model="localSelectedCompany" :style="{ width:'75%', float:'left' }" :placeholder="placeholder" not-found-text="No companies found" filterable>
            <Option 
                v-for="company in localfetchedCompanies" 
                :value="JSON.stringify(company)" 
                :key="company.id">{{ company.name }}
            </Option>
        </Select>
        <!-- Add Company Button -->
        <Poptip :style="{ float:'left' }" class="ml-1" word-wrap width="200" trigger="hover" content="Add a new client">
            <Button class="pt-1 pb-1" icon="ios-add" type="dashed" size="small" @click="isOpenCreateClientModal = true">Add</Button>
        </Poptip>
    </div>

</template>

<script>
    export default {
        props: {
            fetchedCompanies:{
                type: Array,
                default: () => []
            },
            selectedCompany:{
                type: Object,
                default: null
            },
            placeholder:{
                type:String,
                default: 'Select company'
            }
        },
        data(){
            return {
                localfetchedCompanies: this.getFetchedCompanies(),
                isOpenCreateClientModal: false,
                renderKey: 1,
            }
        },
        watch: {
            //  When the fetchedCompanies changes
            fetchedCompanies: function (val) {
                //  Re-render the component
                this.renderComponent();
            }
        },
        computed:{
            localSelectedCompany:{
                get(){
                    return JSON.stringify(this.selectedCompany);
                },
                set(val){
                    var updatedCompany = JSON.parse(val);
                    this.$emit('updated',  updatedCompany);
                }
            },
            modalVisible:{
                get(){
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
                }
            }
        },
        methods: {
            getFetchedCompanies(){
                if(this.fetchedCompanies.length){
                    return this.fetchedCompanies.map(item => JSON.stringify(item));
                }else{
                    return [];
                }
            },
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting companies...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get currencies
                        self.localfetchedCompanies = data.data;

                        console.log('New fetched companies');

                        console.log(self.localfetchedCompanies);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('companySelector.vue - Error getting companies...');
                        console.log(response);    
                    });
            },
            renderComponent: function(){
                console.log('Re-rendering companies');

                this.localfetchedCompanies = this.getFetchedCompanies();

                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            if(!this.fetchedCompanies.length){
                this.fetch();
            }
        }
    };
</script>