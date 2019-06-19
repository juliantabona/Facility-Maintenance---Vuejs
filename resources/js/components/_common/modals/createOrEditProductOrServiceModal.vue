<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :modalClosable="false"
            :isSaving="isCreating || isSaving" 
            :hideModal="hideModal"
            :title="title"
            :okText="okText" cancelText="Cancel"
            @on-ok="handleResponse()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Create/Edit Product -->
                <productWidget 
                    :editMode="true"
                    :productId="(editableProduct || {}).id"
                    :hideSaveBtn="true"
                    :hideSummaryToggle="false"
                    :activateSummaryMode="true"
                    :canSaveOrCreate="isCreating || isSaving"
                    @created:product="getCreatedEntity($event)"
                    @updated:product="getUpdatedEntity($event)">
                </productWidget>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Modal Structure  */
    import mainModal from './main.vue';

    /*  Widgets  */
    import productWidget from './../../../widgets/product/show/productDetails.vue';

    export default {
        props:{
            //  This holds the product/service to be edited if the entity exists
            editableProduct: {
                type: Object,
                default: null
            }
        },
        components: { mainModal, productWidget  },
        data(){
            return{
                hideModal: false,
                isCreating: false,
                isSaving: false,
            }
        },
        computed: {
            title: function(){

                if( this.editableProduct ){
                    return this.editableProduct.type == 'product' ? 'Edit Product': 'Edit Service';
                }else{
                    return 'Add Product/Service';
                }

            },
            okText: function(){
                if( this.editableProduct ){
                    return 'Save Changes';
                }else{
                    return 'Create';
                }
            }
        },
        methods: {
            handleResponse(){
                if( this.editableProduct ){
                    this.isSaving = true;
                }else{
                    this.isCreating = true;
                }
            },
            //  Handle the updated product
            getUpdatedEntity(entity){
                this.$emit('updated', entity);
                this.closeModal();
            },
            //  Handle the created product
            getCreatedEntity(entity){
                this.$emit('created', entity);
                this.closeModal();
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>