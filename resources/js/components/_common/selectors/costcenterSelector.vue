<template>

    <!-- CostCenter Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading cost centers...</Loader>
        <Select v-if="localfetchedCostcenters.length" 
                v-model="localSelectedCostCenter" 
                placeholder="Select cost center" 
                not-found-text="No cost centers found" 
                multiple filterable>
            <Option 
                v-for="costcenter in localfetchedCostcenters" 
                :value="JSON.stringify(costcenter)" 
                :key="costcenter.id">{{ costcenter.name }}</Option>
        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedCostCenter:{
                type: Array,
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
                localfetchedCostcenters: [],
                tracker: 0,
                isLoading: false,
            }
        },
        computed:{
            localSelectedCostCenter:{
                get(){
                    var costcenters = [];
                    
                    if( this.selectedCostCenter ){
                        for(var x=0; x < this.localfetchedCostcenters.length; x++){
                            for(var y=0; y < this.selectedCostCenter.length; y++){
                                if(  this.localfetchedCostcenters[x]['id'] == this.selectedCostCenter[y]['id'] ){
                                    costcenters.push( JSON.stringify(this.localfetchedCostcenters[x]) );
                                }
                            }
                            
                        }
                    }
                    
                    return costcenters;
                },
                set(val){
                    if( this.tracker != 0 ){

                        var costcenters = val.map(costcenter => JSON.parse(costcenter));
                        this.$emit('updated:costcenter', costcenters);

                    }

                    this.tracker = this.tracker + 1;

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting costcenters...');

                //  Get the status e.g) client, supplier, e.t.c
                var modelType = this.modelType ? '?modelType='+this.modelType : '';

                //  Additional data to eager load along with each user found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/costcenters'+modelType+connections)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get costcenters
                        self.localfetchedCostcenters = data;

                        console.log('New fetched costcenters');

                        console.log(self.localfetchedCostcenters);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('costcenterSelector.vue - Error getting costcenters...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>