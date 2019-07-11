<template>

    <Row class="border-bottom mb-3 pb-3">

        <!-- Product Reference Number  -->
        <Col span="6">
            <h2 class="text-dark">Product #{{ localProduct.id }}</h2>
        </Col>

        <!-- Invoice Client  -->
        <Col span="5">
            <h6 class="text-secondary">Vendor</h6>
            <h5><router-link :to="{ name: urlName, params: { id: (localProduct.vendor || {}).id } }">{{ vendorName }}</router-link></h5>         
        </Col>

        <!-- Product Status  -->
        <Col span="4">
            <h6 class="text-secondary">Status</h6>
            <h5><ProductStatusTag :product="product"></ProductStatusTag></h5>   
        </Col>

        <!-- Product Due Date  -->
        <Col span="4">
            <h6 class="text-secondary">Created</h6>
            <h5>{{ localProduct.created_at | moment("from", "now")  }}</h5>            
        </Col>

        <!-- Product Menu -->
        <Col span="1">
            <menuToggle :productId="localProduct.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    /*  Statuses   */
    import ProductStatusTag from './../../../components/_common/statuses/ProductStatusTag.vue';  

    /*  Tags   */
    import priorityTag from './../../../components/_common/tags/priorityTag.vue'; 

    /*  Menu Dropdowns   */
    import menuToggle from './../../../components/_common/dropdowns/productMenuDropdown.vue';

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            product: {
                type: Object,
                default: null
            }
        },
        components: { ProductStatusTag, priorityTag, menuToggle },
        data() {
            return {
                localProduct: this.product,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the product
            product: {
                handler: function (val, oldVal) {

                    //  Update the local product value
                    this.localProduct = val;

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    //  Update the edit mode value
                    this.localEditMode = val;
                }
            }
        },
        computed:{
            vendorName: function(){
                if((this.localProduct.vendor || {}).model_type == 'user'){
                    return (this.localProduct.vendor || {}).full_name;
                }else if((this.localProduct.vendor || {}).model_type == 'company'){
                    return (this.localProduct.vendor || {}).name;
                }
            },
            urlName: function(){
                if((this.localProduct.vendor || {}).model_type == 'user'){
                    return 'show-user';
                }else if((this.localProduct.vendor || {}).model_type == 'company'){
                    return 'show-company';
                }
            }
        },
        methods: {
            
        },
        created () {
            
        }
    }
</script>
