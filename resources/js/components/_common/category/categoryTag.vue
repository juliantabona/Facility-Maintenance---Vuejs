<template>
    <span>
        <span v-if="localCategories.length" v-for="(category, index) in localCategories" :key="index">
            <Poptip word-wrap width="200" trigger="hover" :content="category.description">
                <Tag type="border" color="primary">{{ category.name }}</Tag>
            </Poptip>
        </span>
        <Poptip v-if="showEditBtn && !categories.length" word-wrap width="200" trigger="hover" content="Add category">
            <Button icon="ios-add" type="dashed" size="small" @click="isOpenUpdateCategoriesModal = true">Add</Button>
        </Poptip>
        <Poptip v-if="showEditBtn && categories.length" word-wrap width="200" trigger="hover" content="Change the category">
            <Button type="dashed" size="small" @click="isOpenUpdateCategoriesModal = true">
                <Icon type="ios-create-outline" :size="18" class="mr-2"></Icon>
            </Button>
        </Poptip>

        <!-- 
            MODAL TO UPDATE CATEGORIES
        -->
        <updateCategoriesModal
            v-show="isOpenUpdateCategoriesModal" 
            :show="isOpenUpdateCategoriesModal"
            :categories="localCategories"
            v-on:closed="closeModal"
            v-on:updated="updateChanges">
        </updateCategoriesModal>

    </span>
</template>
<script type="text/javascript">

    import updateCategoriesModal from './updateCategoriesModal.vue';

    export default {
        props:{
            categories: {
                type: Array,
                default: () => []
            },
            showEditBtn: {
                type: Boolean,
                default: true
            }
        },
        components: { updateCategoriesModal },
        data(){
            return {
                localCategories: this.categories,
                isOpenUpdateCategoriesModal: false
            }
        },
        methods: {
            updateChanges(newCategories){
                this.localCategories = newCategories;
                this.$emit('updated', newCategories);
                this.closeModal();

            },
            closeModal(){
                this.isOpenUpdateCategoriesModal = !this.isOpenUpdateCategoriesModal;
            }
        }
    }
</script>
