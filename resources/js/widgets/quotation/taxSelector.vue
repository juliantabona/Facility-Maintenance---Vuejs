<template>
    
    <!-- Quotation Tax Selector -->
    <Poptip word-wrap width="200" trigger="hover" content="Create or Apply existing Tax">
        <Select v-if="localFetchedTaxes.length" v-model="localSelectedTaxes" :style="{ width:'100%' }" placeholder="Apply Tax" not-found-text="No taxes found" multiple>
            <Option v-for="item in fetchedTaxes" :value="JSON.stringify(item)" :key="item.id">
                {{ item.name }} ({{ item.rate*100 }}%)
            </Option>
        </Select>
        <Button icon="ios-add" type="dashed" size="small" class="mt-1">Create Tax</Button>
    </Poptip>

</template>
<script>
    export default {
        props: [
            'fetchedTaxes',
            'selectedTaxes'
        ],
        computed:{
            localSelectedTaxes:{
                get(){
                    return this.selectedTaxes.map(item => JSON.stringify(item));
                },
                set(val){
                    var updatedTaxes = val.map(item => JSON.parse(item));
                    this.$emit('updated',  updatedTaxes);
                }
            },
            localFetchedTaxes: function(){
                return this.fetchedTaxes.map(item => JSON.stringify(item));
                
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
    };
</script>