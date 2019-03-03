<template>

    <!-- Phone Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading phones...</Loader>
        <div v-if="localfetchedPhones.length && !isLoading">
            <b class="d-block mb-1">Select Phone</b>
            <Select v-model="localSelectedPhone" 
                    placeholder="Select Phone" 
                    not-found-text="No phones found" 
                    filterable>
                <Option 
                    v-for="phone in localfetchedPhones" 
                    :value="JSON.stringify(phone)" 
                    :disabled="disabledTypes.includes(phone.type)"
                    :key="phone.id">
                    (+{{ phone.calling_code.calling_code }}) {{ phone.number }} - {{ phone.type | capitalize }}
                </Option>

            </Select>
        </div>
        <Alert v-if="!localfetchedPhones.length && !isLoading" show-icon>
            <Icon type="ios-bulb-outline" slot="icon"></Icon>
            <template slot="desc">
                <p>No existing phones found...<span class="btn btn-link" @click="$emit('addNew')">Add New Phone</span></p></template>
        </Alert>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            modelId: {
                type: Number,
                default: null,  
            },
            modelType: {
                type: String,
                default: null,  
            },
            disabledTypes:{
                type: Array,
                default: () => { return [] },  
            },
        },
        components: { Loader },
        data(){
            return {
                user: auth.user,
                localfetchedPhones: [],
                isLoading: false,
            }
        },
        computed:{
            localSelectedPhone:{
                get(){
                    return JSON.stringify(this.selectedPhone);
                },
                set(val){
                    if(val != ''){
                        var updatedPhone = JSON.parse(val);
                        this.$emit('selected:phone',  updatedPhone );
                    }

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting existing phones...');

                //  Additional data to eager load along with each mobile money phone found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/phones?modelId='+this.modelId+'&modelType='+this.modelType)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get phones
                        self.localfetchedPhones = data;
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('mobileMoneyPhoneSelector.vue - Error getting getting existing phones...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>