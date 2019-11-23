<template>

    <!-- Drawer -->
    <mainDrawer 
        width="500"
        v-bind="$props"
        :title="mainTitle"
        :mask-closable="false"
        :showDrawer="showDrawer"
        @visibility="$emit('visibility', $event)">

        <template v-if="showDrawer">

            <!-- Edit Product -->
            <editOrUpdateProductForm
                :store="store"
                :product="product"
                :createUrl="createUrl"
                :updateUrl="updateUrl"
                :ussdInterface="ussdInterface"
                @createSuccess="$emit('createSuccess', $event)"
                @updateSuccess="$emit('updateSuccess', $event)">
            </editOrUpdateProductForm>                

        </template>

    </mainDrawer> 
        
</template>
<script>

    /*  Forms  */
    import editOrUpdateProductForm from './../forms/product/editOrUpdateProduct.vue'; 

    /*  Drawer Structure  */
    import mainDrawer from './main.vue';

    export default {
        props:{

            /*  The store  */
            store: {
                type: Object,
                default: null
            },

            /*  The ussd interface of the store  */
            ussdInterface: {
                type: Object,
                default: null
            },

            /*  The url to make a Post Request to create a product  */
            createUrl: {
                type: String,
                default: null
            },

            /*  The url to make a Post Request to update a product  */
            updateUrl: {
                type: String,
                default: null
            },

            /*  The product to update  */
            product: {
                type: Object,
                default: null
            },

            /*  Display Drawer or not.  */
            showDrawer: {
                type: Boolean,
                default: false
            },

            /*  The title of Drawer. It is invalid 
             *   if header slot is set.
             */
            title: {
                type: String,
                default: null,
            },

            /*  Whether to display the mask layer 
             *  (The dark overlay background).
             */
            mask:{
                type: Boolean,
                default: true,
            }
        },
        components: { editOrUpdateProductForm, mainDrawer },
        data(){
            return{

                localProduct: this.product

            }
        },
        watch: {
            /*  Keep track of changes on the product.  */
            product: {

                handler: function (val, oldVal) {

                    /*  Update the localProduct  */
                    this.localProduct = val;

                },
                deep: true

            }
        },
        computed: {
            mainTitle(){
                //  If we already has a title provided
                if(this.title){

                    //  Return the provided title
                    return this.title;

                }else{

                    //  If we have a product then use "Edit Product" otherwise "Create Product" as the title
                    return this.localProduct ? 'Edit Product' : 'Create Product';

                }
            }
        },
    }
</script>