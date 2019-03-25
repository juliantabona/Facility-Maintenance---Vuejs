<template>

    <!-- Staff Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading staff...</Loader>
        <Select v-if="localfetchedStaff.length" 
                v-model="localSelectedStaff" 
                placeholder="Select staff" 
                not-found-text="No staff found" 
                multiple filterable>
            <Option 
                v-for="staff in localfetchedStaff" 
                :value="JSON.stringify(staff)" 
                :key="staff.id">{{ staff.full_name }}{{ staff.position ? ' ('+staff.position+')' : '' }}</Option>
        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedStaff:{
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
                localfetchedStaff: [],
                tracker: 0,
                isLoading: false,
            }
        },
        computed:{
            localSelectedStaff:{
                get(){
                    var staff = [];
                    
                    if( this.selectedStaff ){
                        for(var x=0; x < this.localfetchedStaff.length; x++){
                            for(var y=0; y < this.selectedStaff.length; y++){
                                if(  this.localfetchedStaff[x]['id'] == this.selectedStaff[y]['id'] ){
                                    staff.push( JSON.stringify(this.localfetchedStaff[x]) );
                                }
                            }
                            
                        }
                    }
                    
                    return staff;
                },
                set(val){
                    if( this.tracker != 0 ){

                        var staff = val.map(staff => JSON.parse(staff));
                        this.$emit('updated:staff', staff);

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

                console.log('Start getting staff...');

                //  Get the status e.g) client, supplier, e.t.c
                var modelType = this.modelType ? '?modelType='+this.modelType : '';

                //  Additional data to eager load along with each user found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies/staff'+modelType+connections)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get staff
                        self.localfetchedStaff = data;

                        console.log('New fetched staff');

                        console.log(self.localfetchedStaff);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('staffSelector.vue - Error getting staff...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>