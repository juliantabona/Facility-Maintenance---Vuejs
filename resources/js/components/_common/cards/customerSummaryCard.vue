<style scoped>

</style>

<template>
    <Card class="full-height">
        <h4 class="text-primary mb-0">Customer</h4>
        <span class="d-block pb-2 mb-2 border-bottom">
            <span class="font-weight-bold">
                <Icon type="ios-contact-outline" :size="20"/>
            </span> 
            {{ customerName }}
        </span>
        <span class="d-block">
            <span class="font-weight-bold">
                <Icon type="ios-call-outline" :size="20"/>
            </span>
            {{ customerPhoneNumbers }}
        </span>
        <span class="d-block">
            <span class="font-weight-bold">
                <Icon type="ios-mail-outline" :size="20" />
            </span> 
            {{ (localCustomer || {}).email ||  '___' }}
        </span>
        
        <span class="d-flex pt-2 mt-2 border-top">
            <Icon type="ios-pin-outline" :size="20" class="mr-1" />
            <span>{{ ( (customerAddress.address_1 || customerAddress.address_2) ) ||  '___' }}</span> 
        </span>
        <span class="d-block pt-2 mt-2 border-top">
            <span class="d-block">
                <span class="font-weight-bold">Country: </span>{{ (customerAddress || {}).country ||  '___' }}
            </span> 
            <span class="d-block">
                <span class="font-weight-bold">Province: </span>{{ (customerAddress || {}).province ||  '___' }}
            </span> 
            <span class="d-block">
                <span class="font-weight-bold">City: </span>{{ (customerAddress || {}).city ||  '___' }}
            </span> 
        </span>
    </Card>
</template>

<script>

    export default {
        props:{
            customer: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localCustomer: this.customer
            }
        },
        computed: {
            customerName(){
                return (this.localCustomer || {}).name;
            },
            customerAddress(){

                return ((this.localCustomer || {}).default_address || {});
            },
            customerPhoneNumbers(){
                var phoneList = '';
                var phones = (this.localCustomer || {}).phones || [];
                
                for( var x=0; x < phones.length; x++ ){
                    
                    if( phones[x].type == 'mobile' || phones[x].type == 'tel' ){

                        if(x != 0){
                            phoneList = phoneList + ', ';
                        }

                        phoneList = phoneList + '(+' + phones[x]['calling_code'] + ') ' + phones[x]['number'];
                        
                    }
                        
                }

                return phoneList;

            }
        }
    };
  
</script>