<template>
    <span>
        <span v-if="priority">
            <Poptip word-wrap width="200" trigger="hover" :content="priority.description">
                <Tag class="priority_tag" :style="{ 
                    background: priority.color_code + '10 !important',
                    border: '1px solid '+priority.color_code + ' !important'}">
                    <span :style="{ color: priority.color_code }">{{ priority.name }}</span>
                </Tag>
            </Poptip>
        </span>
        <Poptip v-if="showEditBtn && !priority" word-wrap width="200" trigger="hover" content="Add priority status">
            <Button icon="ios-add" type="dashed" size="small" @click="isOpenUpdatePrioritiesModal = true">Add</Button>
        </Poptip>
        <Poptip v-if="showEditBtn && priority" word-wrap width="200" trigger="hover" content="Change the priority status">
            <Button type="dashed" size="small" @click="isOpenUpdatePrioritiesModal = true">
                <Icon type="ios-create-outline" :size="18" class="mr-2"></Icon>
            </Button>
        </Poptip>
        <!-- 
            MODAL TO UPDATE PRIORITIES
        -->
        <updatePrioritiesModal
            v-show="isOpenUpdatePrioritiesModal" 
            :show="isOpenUpdatePrioritiesModal"
            :priority="localPriority"
            v-on:closed="closeModal"
            v-on:updated="updateChanges">
        </updatePrioritiesModal>

    </span>
</template>
<script type="text/javascript">

    import updatePrioritiesModal from './updatePrioritiesModal.vue';

    export default {
        props:{
            priority: {
                type: Object,
                default: null
            },
            showEditBtn: {
                type: Boolean,
                default: true
            }
        },
        components: { updatePrioritiesModal },
        data(){
            return {
                localPriority: this.priority,
                isOpenUpdatePrioritiesModal: false
            }
        },
        methods: {
            updateChanges(newPriority){
                this.localPriority = newPriority;
                this.$emit('updated', newPriority);
                this.closeModal();

            },
            closeModal(){
                this.isOpenUpdatePrioritiesModal = !this.isOpenUpdatePrioritiesModal;
            }
        }
    }
</script>
