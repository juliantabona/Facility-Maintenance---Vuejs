<template>

    <Select v-model="localSelectedType" 
            placeholder="Select type..." 
            not-found-text="No types found"
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
        props: {
            selectedType: {
                type: String,
                default: null
            }
        },
        data(){
            return{
                localSelectedType: this.selectedType,
                clientTypes: [
                    { name: 'Product', value: 'product'},
                    { name: 'Service', value: 'service'}
                ]
            }
        },
        watch: {

            //  Watch for changes on the selectedType value
            selectedType: {
                handler: function (val, oldVal) {

                    this.localSelectedType = val;

                }
            }
        }
    }
</script>