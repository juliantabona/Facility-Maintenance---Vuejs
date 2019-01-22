<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Calling Code Selector -->
    <Row :gutter="5">
        <Col :span="24">
            <Loader v-if="isLoadingCallingCodes" :loading="isLoadingCallingCodes" type="text" class="text-left">Loading...</Loader>
        </Col>
        <Col :span="6" v-if="!isLoadingCallingCodes && formattedCallingCodes.length">
            <Select v-model="localSelectedCallingCode" :style="{ width:'100%' }" placeholder="Select" not-found-text="No calling codes found" filterable>
                <Option
                    v-for="(item, index) in fetchedCallingCodes" 
                    :value="JSON.stringify(item)" 
                    :label="'+'+item.calling_code"
                    :key="index">
                    <span style="width:30px;" class="mr-1" v-html="item.flag"></span>
                    <span>{{ item.country }} (+{{ item.calling_code }})</span>
                </Option>
            </Select>
        </Col>
        <Col :span="18" v-if="!isLoadingCallingCodes && formattedCallingCodes.length">
            <el-input v-model="localPhoneNumber" size="small" style="width:100%" placeholder="Enter phone number"></el-input>
        </Col>
    </Row>
</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue'; 

    export default {
        components: { 
            Loader
        },
        props: [
            'selectedCallingCode',
            'phoneNumber'
        ],
        data(){
            return {
                localPhoneNumber: this.phoneNumber,
                updatedCallingCode: null,
                isLoadingCallingCodes: false,
                fetchedCallingCodes: []
            }
        },
        computed:{
            localSelectedCallingCode:{
                get(){
                    return JSON.stringify(this.selectedCallingCode);
                },
                set(val){
                    console.log('stage 1');
                    console.log(JSON.parse(val));
                    var updatedCallingCode = JSON.parse(val);
                    this.$emit('updated',  updatedCallingCode);
                }
            },
            formattedCallingCodes: function(){
                if(this.fetchedCallingCodes.length){
                    return this.fetchedCallingCodes.map(item => JSON.stringify(item));
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
            fetchCallingCodes() {
                const self = this;

                //  Start loader
                self.isLoadingCallingCodes = true;

                console.log('Start getting calling codes...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/callingcodes')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCallingCodes = false;

                        //  Get calling codes
                        self.fetchedCallingCodes = data;

                        console.log('New fetched calling codes');

                        console.log(self.fetchedCallingCodes);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCallingCodes = false;

                        console.log('callingCodesSelector.vue - Error getting calling codes...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetchCallingCodes();
        }
    };
</script>