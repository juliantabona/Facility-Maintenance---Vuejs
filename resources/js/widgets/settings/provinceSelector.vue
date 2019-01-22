<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Provience Selector -->
    <div>
        <Loader v-if="isLoadingProviencies" :loading="isLoadingProviencies" type="text" class="text-left">Loading proviencies...</Loader>
        <Select v-if="!isLoadingProviencies" v-model="localSelectedProvience" :style="{ width:'100%' }" placeholder="Select provience" not-found-text="No proviencies found" filterable>
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

    import Loader from './../../components/_common/loader/Loader.vue'; 

    export default {
        components: { 
            Loader
        },
        props: {
            selectedProvience: {
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
                updatedProvience: null,
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
            localSelectedProvience:{
                get(){
                    return this.selectedProvience;
                },
                set(val){
                    var updatedProvience = val;
                    this.$emit('updated',  updatedProvience);
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
                    console.log('The provience is: ' + this.selectedCountry);

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

                            console.log('provienceSelector.vue - Error getting proviencies...');
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