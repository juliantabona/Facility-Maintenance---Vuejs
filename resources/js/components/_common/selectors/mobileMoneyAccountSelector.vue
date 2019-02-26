<template>

    <!-- Account Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading accounts...</Loader>
        <Select v-if="localfetchedAccounts.length" 
                v-model="localSelectedAccount" 
                placeholder="Select Account" 
                not-found-text="No accounts found" 
                filterable>
            <Option 
                v-for="account in localfetchedAccounts" 
                :value="JSON.stringify(account)" 
                :key="account.id">
                {{ account.mobile_money_account.account_name }} - ({{ account.mobile_money_account.status }})
            </Option>

        </Select>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedAccount:{
                type: Object,
                default: null
            }
        },
        components: { Loader },
        data(){
            return {
                user: auth.user,
                localfetchedAccounts: [],
                isLoading: false,
            }
        },
        computed:{
            localSelectedAccount:{
                get(){
                    return JSON.stringify(this.selectedAccount);
                },
                set(val){
                    if(val != ''){
                        var updatedAccount = JSON.parse(val);
                        this.$emit('updated:account',  updatedAccount );
                    }

                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting mobile money accounts...');

                //  Additional data to eager load along with each mobile money account found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies/'+this.user.company_id+'/wallets'+connections)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get accounts
                        self.localfetchedAccounts = data;
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('mobileMoneyAccountSelector.vue - Error getting mobile money accounts...');
                        console.log(response);    
                    });
            }
        },
        created(){
            this.fetch();
        }
    };
</script>