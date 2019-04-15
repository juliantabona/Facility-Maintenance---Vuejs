<template>

    <!-- Individual Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading individuals...</Loader>
        <Select v-if="localfetchedUsers.length" 
                v-model="localSelectedUser" 
                placeholder="Select individual" 
                not-found-text="No individuals found" 
                filterable>
            <Option 
                v-for="user in localfetchedUsers" 
                :value="JSON.stringify(user)" 
                :key="user.id">{{ user.first_name }} {{ user.last_name }}
            </Option>
        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedUser:{
                type: Object,
                default: null
            }
        },
        components: { Loader },
        data(){
            return {
                localfetchedUsers: [],
                isLoading: false,
            }
        },
        computed:{
            localSelectedUser:{
                get(){
                    return JSON.stringify(this.selectedUser);
                },
                set(val){
                    if(val != ''){
                        var updatedUser = JSON.parse(val);
                        this.$emit('updated:user',  updatedUser );
                    }

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting individuals...');

                //  Additional data to eager load along with each user found
                var connections = '&connections=phones';

                //  Settings to prevent pagination
                var pagination = (connections ? '&': '') + 'paginate=0';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/users?allocation=company&type=client,supplier'+connections+pagination)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get currencies
                        self.localfetchedUsers = data;

                        console.log('New fetched companies');

                        console.log(self.localfetchedUsers);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('individualSelector.vue - Error getting individuals...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>