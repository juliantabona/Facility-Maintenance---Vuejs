<template>

    <!-- Priority Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading priorities...</Loader>
        <Select v-if="localfetchedPriorities.length" 
                v-model="localSelectedPriority" 
                placeholder="Select priority" 
                not-found-text="No priorities found" 
                filterable>
            <Option 
                v-for="priority in localfetchedPriorities" 
                :value="JSON.stringify(priority)" 
                :key="priority.id">{{ priority.name }}</Option>
        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedPriority:{
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
                localfetchedPriorities: [],
                isLoading: false,
            }
        },
        computed:{
            localSelectedPriority:{
                get(){
                    if( this.selectedPriority ){

                        if( this.selectedPriority.length ){
                            
                            var priority;
                            
                            for(var x=0; x < this.localfetchedPriorities.length; x++){
                                if(  this.localfetchedPriorities[x]['id'] == this.selectedPriority[0]['id'] ){
                                    priority = JSON.stringify(this.localfetchedPriorities[x]);
                                }  
                            }

                        }
                        
                        return priority;

                    }
                },
                set(val){
                    if(val != ''){
                        var updatedPriority = [JSON.parse(val)];
                        this.$emit('updated:priority',  updatedPriority );
                    }

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting priorities...');

                //  Get the status e.g) client, supplier, e.t.c
                var modelType = this.modelType ? 'modelType='+this.modelType : '';

                //  Additional data to eager load along with each user found
                var connections = '';

                //  Settings to prevent pagination
                var pagination = (this.modelType || connections ? '&': '') + 'paginate=0';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/priorities?'+modelType+connections+pagination)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get priorities
                        self.localfetchedPriorities = data;

                        console.log('New fetched priorities');

                        console.log(self.localfetchedPriorities);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('prioritySelector.vue - Error getting priorities...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>