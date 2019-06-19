<template>

    <Select v-model="localSelectedClientType" 
            placeholder="Select client/supplier..." 
            not-found-text="No client types found"
            @on-change="$emit('on-change', $event)">
        <Option 
            v-for="(client, i) in clientTypes" 
            :value="client.value" 
            :key="i">{{ client.name }}
        </Option>
    </Select>

</template>
<script>

    export default {
        props:{
            selectedClientType: {
                type: String,
                default: null
            }
        },
        data(){
            return{
                localSelectedClientType: '',
                clientTypes: [
                    { name: 'Client/Customer', value: 'client'},
                    { name: 'Supplier/vendor', value: 'supplier'}
                ]
            }
        },
        mounted(){
            if( this.selectedClientType ){
                for(var x = 0; x < (this.clientTypes || {}).length; x++){
                    if(this.clientTypes[x].value == this.selectedClientType){
                        this.localSelectedClientType = this.clientTypes[x].value;
                        this.$emit('on-change', this.localSelectedClientType);
                    }
                }
            }
        }
    }
</script>