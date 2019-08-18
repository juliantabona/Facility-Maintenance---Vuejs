<template>

    <Select v-model="localSelectedType" 
            placeholder="Select type..." 
            not-found-text="No types found"
            @on-change="$emit('on-change', $event)">
        <Option 
            v-for="(product, i) in productTypes" 
            :value="product.value" 
            :key="i">{{ product.name }}
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
                productTypes: [
                    { name: 'Physical', value: 'physical'},
                    { name: 'Service', value: 'service'},
                    { name: 'Event', value: 'event'},
                    { name: 'Ticket', value: 'ticket'},
                    { name: 'Booking', value: 'booking'},
                    { name: 'Donation', value: 'donation'},
                    { name: 'Membership', value: 'membership'}
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