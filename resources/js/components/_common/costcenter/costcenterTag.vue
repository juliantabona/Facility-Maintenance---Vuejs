<template>
    <span>
        <span v-if="localCostCenters.length" v-for="(costcenter, index) in localCostCenters" :key="index">
            <Poptip word-wrap width="200" trigger="hover" :content="costcenter.description">
                <Tag type="border" color="primary">{{ costcenter.name }}</Tag>
            </Poptip>
        </span>
        <Poptip v-if="showEditBtn && !costcenters.length" word-wrap width="200" trigger="hover" content="Add Cost Center">
            <Button icon="ios-add" type="dashed" size="small" @click="isOpenUpdateCostCentersModal = true">Add</Button>
        </Poptip>
        <Poptip v-if="showEditBtn && costcenters.length" word-wrap width="200" trigger="hover" content="Change the Cost Center">
            <Button type="dashed" size="small" @click="isOpenUpdateCostCentersModal = true">
                <Icon type="ios-create-outline" :size="18" class="mr-2"></Icon>
                </Button>
        </Poptip>

        <!-- 
            MODAL TO UPDATE COST CENTERS
        -->
        <updateCostCentersModal
            v-show="isOpenUpdateCostCentersModal" 
            :show="isOpenUpdateCostCentersModal"
            :costcenters="localCostCenters"
            v-on:closed="closeModal"
            v-on:updated="updateChanges">
        </updateCostCentersModal>

    </span>
</template>
<script type="text/javascript">

    import updateCostCentersModal from './updateCostCentersModal.vue';

    export default {
        props:{
            costcenters: {
                type: Array,
                default: () => []
            },
            showEditBtn: {
                type: Boolean,
                default: true
            }
        },
        components: { updateCostCentersModal },
        data(){
            return {
                localCostCenters: this.costcenters,
                isOpenUpdateCostCentersModal: false
            }
        },
        methods: {
            updateChanges(newCostCenters){ 
                this.localCostCenters = newCostCenters;
                this.$emit('updated', newCostCenters);
                this.closeModal();
            },
            closeModal(){
                this.isOpenUpdateCostCentersModal = !this.isOpenUpdateCostCentersModal;
            }
        }
    }
</script>

