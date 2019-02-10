<template>

    <Select v-model="selectedClientType" 
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
        data(){
            return{
                selectedClientType: '',
                clientTypes: [
                    { name: 'Client/Customer', value: 'client'},
                    { name: 'Supplier/vendor', value: 'supplier'}
                ]
            }
        }
    }
</script>