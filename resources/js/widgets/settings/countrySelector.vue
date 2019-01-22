<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Country Selector -->
    <div>
        <Loader v-if="isLoadingCountries" :loading="isLoadingCountries" type="text" class="text-left">Loading coutries...</Loader>
        <Select v-if="!isLoadingCountries && formattedCountries.length" v-model="localSelectedCountry" :style="{ width:'100%' }" placeholder="Select country" not-found-text="No countries found" filterable>
            <Option 
                v-for="(item, index) in fetchedCountries" 
                :value="item.name" 
                :label="item.name" 
                :key="index">
                <span style="width:30px;" class="mr-1" v-html="item.flag"></span>
                <span>{{ item.name }}</span>
            </Option>
        </Select>
    </div>
</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue'; 

    export default {
        components: { 
            Loader
        },
        props: [
            'selectedCountry'
        ],
        data(){
            return {
                updatedCountry: null,
                isLoadingCountries: false,
                fetchedCountries: []
            }
        },
        computed:{
            localSelectedCountry:{
                get(){
                    return this.selectedCountry;
                },
                set(val){
                    var updatedCountry = val;
                    this.$emit('updated',  updatedCountry);
                }
            },
            formattedCountries: function(){
                if(this.fetchedCountries.length){
                    return this.fetchedCountries;
                }
                    
                return [];
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
            fetchCountries() {
                const self = this;

                //  Start loader
                self.isLoadingCountries = true;

                console.log('Start getting countries...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/countries')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCountries = false;

                        //  Get countries
                        self.fetchedCountries = data;

                        console.log('New fetched countries');

                        console.log(self.fetchedCountries);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCountries = false;

                        console.log('countrySelector.vue - Error getting countries...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetchCountries();
        }
    };
</script>