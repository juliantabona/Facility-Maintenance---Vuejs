<template>
    
    <!-- Currency Selector -->
    <Select v-if="localFetchedCurrencies.length" v-model="localSelectedCurrency" :style="{ width:'100%' }" placeholder="Select currency" not-found-text="No currencies found" filterable>
        <Option 
            v-for="item in fetchedCurrencies" 
            :value="JSON.stringify(item)" 
            :key="item.id">{{ item.country }} ({{ item.currency.iso.code }})</Option>
    </Select>

</template>
<script>
    export default {
        props: [
            'fetchedCurrencies',
            'selectedCurrency'
        ],
        data(){
            return {
                renderKey: 1,
            }
        },
        watch: {
            //  When the fetchedCurrencies changes
            fetchedCurrencies: function (val) {
                //  Re-render the component
                this.renderComponent();
            }
        },
        computed:{
            localSelectedCurrency:{
                get(){
                    if(this.selectedCurrency){
                        return JSON.stringify(this.selectedCurrency);
                    }
                },
                set(val){
                    if(val){
                        console.log(JSON.parse(val));
                        var updatedCurrencies = JSON.parse(val);
                        this.$emit('updated',  updatedCurrencies);
                    }
                }
            },
            localFetchedCurrencies: function(){
                if(this.fetchedCurrencies.length){
                    return this.fetchedCurrencies.map(item => JSON.stringify(item));
                }
            },
            modalVisible:{
                get(){
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
                }
            }
        },
        methods: {
            renderComponent: function(){
                console.log('Re-rendering currencies');
                //  Re-render the component
                this.renderKey++;
            }
        }
    };
</script>