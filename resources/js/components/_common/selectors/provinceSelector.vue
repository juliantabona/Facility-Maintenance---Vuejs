<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Province Selector -->
    <div>
        <Loader v-if="isLoadingProviencies" :loading="isLoadingProviencies" type="text" class="text-left">Loading proviencies...</Loader>
        <Select v-if="!isLoadingProviencies" v-model="localSelectedProvince" :style="{ width:'100%' }" placeholder="Select province" not-found-text="No proviencies found" filterable>
            <Option 
                v-for="(item, index) in fetchedProviencies" 
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
            selectedProvince: {
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
                updatedProvince: null,
                isLoadingProviencies: false,
                fetchedProviencies: []
            }
        },
        watch: {
            selectedCountry: function (val) {
                //  Re-fetch the country associated proviencies
                this.fetchProviencies();
            }
        },
        computed:{
            localSelectedProvince:{
                get(){
                    return this.selectedProvince;
                },
                set(val){
                    var updatedProvince = val;
                    this.$emit('updated',  updatedProvince);
                }
            },
            formattedProviencies: function(){
                if(this.fetchedProviencies.length){
                    return this.fetchedProviencies;
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
            fetchProviencies() {

                if( this.selectedCountry ){

                    const self = this;

                    //  Start loader
                    self.isLoadingProviencies = true;

                    console.log('Start getting proviencies...');
                    console.log('The province is: ' + this.selectedCountry);

                    var selectedCountry = '?country=' + this.selectedCountry; 

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/states' + selectedCountry)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingProviencies = false;

                            //  Get proviencies
                            self.fetchedProviencies = data.states;

                            console.log('New fetched proviencies');

                            console.log(self.fetchedProviencies);
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingProviencies = false;

                            console.log('provinceSelector.vue - Error getting proviencies...');
                            console.log(response);    
                        });

                }
            }
        },
        created(){
            this.fetchProviencies();
        }
    };
</script>