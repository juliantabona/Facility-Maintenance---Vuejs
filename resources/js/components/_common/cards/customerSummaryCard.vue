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
            <span>{{ customerAddress ||  '___' }}</span> 
        </span>
        <span class="d-block pt-2 mt-2 border-top">
            <span class="d-block">
                <span class="font-weight-bold">Country: </span>{{ (localCustomer || {}).country ||  '___' }}
            </span> 
            <span class="d-block">
                <span class="font-weight-bold">Province: </span>{{ (localCustomer || {}).province ||  '___' }}
            </span> 
            <span class="d-block">
                <span class="font-weight-bold">City: </span>{{ (localCustomer || {}).city ||  '___' }}
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
                return ( (this.localCustomer || {}).first_name || (this.localCustomer || {} ).name )
                        +' '+ (this.localCustomer || {}).last_name;
            },
            customerAddress(){
                return ( (this.localCustomer || {}).address_1 || (this.localCustomer || {}).address_2 );
            },
            customerPhoneNumbers(){
                var phoneList = '';
                var phones = (this.localCustomer || {}).phones || [];
                
                for( var x=0; x < phones.length; x++ ){
                    
                    if( phones[x].type != 'fax' ){

                        if(x != 0){
                            phoneList = phoneList + ', ';
                        }

                        phoneList = phoneList + '(+' + phones[x]['calling_code']['calling_code'] + ') ' + phones[x]['number'];
                        
                    }
                        
                }

                return phoneList;

            }
        }
    };
  
</script>