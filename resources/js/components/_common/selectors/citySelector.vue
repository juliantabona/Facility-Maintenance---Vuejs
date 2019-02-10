<template>
    
    <!-- City Selector -->
    <div>
        <Loader v-if="isLoadingCities" :loading="isLoadingCities" type="text" class="text-left">Loading cities...</Loader>
        <Select v-if="!isLoadingCities" v-model="localSelectedCity" :style="{ width:'100%' }" placeholder="Select city" not-found-text="No cities found" filterable>
            <Option 
                v-for="(item, index) in fetchedCities" 
                :value="item" 
                :label="item" 
                :key="index">
                <span>{{ item }}</span>
            </Option>
        </Select>
    </div>
</template>

<script>

    import Loader from './../loaders/Loader.vue'; 

    export default {
        components: { 
            Loader
        },
        props: {
            selectedCity: {
                type: String,
                default: ''
            },
            selectedCountry: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                updatedCity: null,
                isLoadingCities: false,
                fetchedCities: []
            }
        },
        watch: {
            selectedCountry: function (val) {
                //  Re-fetch the country associated cities
                this.fetchCities();
            }
        },
        computed:{
            localSelectedCity:{
                get(){
                    return this.selectedCity;
                },
                set(val){
                    var updatedCity = val;
                    this.$emit('updated',  updatedCity);
                }
            },
            formattedCities: function(){
                if(this.fetchedCities.length){
                    return this.fetchedCities;
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
            fetchCities() {

                if( this.selectedCountry ){

                    const self = this;

                    //  Start loader
                    self.isLoadingCities = true;

                    console.log('Start getting cities...');
                    console.log('The city is: ' + this.selectedCountry);

                    var selectedCountry = '?country=' + this.selectedCountry; 

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/cities' + selectedCountry)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingCities = false;

                            //  Get cities
                            self.fetchedCities = data.cities;

                            console.log('New fetched cities');

                            console.log(self.fetchedCities);
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingCities = false;

                            console.log('citySelector.vue - Error getting cities...');
                            console.log(response);    
                        });

                }
            }
        },
        created(){
            this.fetchCities();
        }
    };
</script>