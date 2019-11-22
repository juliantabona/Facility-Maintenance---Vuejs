<template>

    <!-- Drawer -->
    <mainDrawer 
        width="500"
        v-bind="$props"
        :mask-closable="false"
        :showDrawer="showDrawer"
        @visibility="$emit('visibility', $event)">

        <template v-if="showDrawer">

            <!-- Edit Product -->
            <updateProductForm 
                v-if="product"
                :product="product"
                @updateSuccess="$emit('updateSuccess', $event)">
            </updateProductForm>                

        </template>

    </mainDrawer> 
        
</template>
<script>

    /*  Forms  */
    import updateProductForm from './../forms/update-product/updateProduct.vue'; 

    /*  Drawer Structure  */
    import mainDrawer from './main.vue';

    export default {
        props:{

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
                default: 'Edit Product',
            },

            /*  The product to edit  */
            product: {
                type: Object,
                default: null
            },

            /*  Whether to display the mask layer 
             *  (The dark overlay background).
             */
            mask:{
                type: Boolean,
                default: true,
            }
        },
        components: { updateProductForm, mainDrawer  },
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
        }
    }
</script>