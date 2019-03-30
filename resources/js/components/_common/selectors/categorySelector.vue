<template>

    <!-- Category Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading categories...</Loader>
        <Select v-if="localfetchedCategories.length" 
                v-model="localSelectedCategory" 
                placeholder="Select category" 
                not-found-text="No categories found" 
                multiple filterable>
            <Option 
                v-for="category in localfetchedCategories" 
                :value="JSON.stringify(category)" 
                :key="category.id">{{ category.name }}</Option>
        </Select>
        
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedCategory:{
                type: Array,
                default: null
            },
            modelType: {
                type: String,
                default: ''   
            },
            tracker: {
                type: Number,
                default: 0   
            },
        },
        components: { Loader },
        data(){
            return {
                localfetchedCategories: [],
                localTracker: this.tracker,
                isLoading: false,
            }
        },
        computed:{
            localSelectedCategory:{
                get(){
                    var categories = [];
                    
                    if( this.selectedCategory ){

                        if( this.selectedCategory.length ){

                            for(var x=0; x < this.localfetchedCategories.length; x++){
                                for(var y=0; y < this.selectedCategory.length; y++){
                                    if(  this.localfetchedCategories[x]['id'] == this.selectedCategory[y]['id'] ){
                                        categories.push( JSON.stringify(this.localfetchedCategories[x]) );
                                    }
                                }
                                
                            }

                        }
                    }
                    
                    return categories;
                },
                set(val){
                    if( this.localTracker != 0 ){
                        
                        var categories = val.map(category => JSON.parse(category));
                        this.$emit('updated:category', categories);

                    }

                    this.localTracker = this.localTracker + 1;

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting categories...');

                //  Get the status e.g) client, supplier, e.t.c
                var modelType = this.modelType ? 'modelType='+this.modelType : '';

                //  Additional data to eager load along with each user found
                var connections = '';

                //  Settings to prevent pagination
                var pagination = (this.modelType || connections ? '&': '') + 'paginate=0';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/categories?'+modelType+connections+pagination)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get categories
                        self.localfetchedCategories = data;

                        console.log('New fetched categories');

                        console.log(self.localfetchedCategories);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('categorySelector.vue - Error getting categories...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>