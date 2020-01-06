<template>

    <div>

        <!-- Order Heading / Add Order Button -->
        <Row v-if="!isViewingAnOrder" class="border-bottom pb-3">

            <Col :span="4">

                <!-- Order Heading -->
                <h2 class="font-weight-bold pl-4">Orders</h2>

            </Col>

            <Col :span="20">
                <div class="clearfix">
                    
                    <!-- Add Order Button -->
                    <basicButton @click.native="$router.push({ name:'create-order' })" 
                                size="large" class="float-right">
                                <span>+ Create Order</span>
                    </basicButton>

                </div>
            </Col>

        </Row>

        <!-- Order Table List With Search Input, Filters & Sort Functionality -->
        <Row>

            <Col :span="24">

                <!-- Invoice Table Widget -->
                <invoiceTableWidget 
                    :ordersUrl="ordersUrl" 
                    @viewingSingleRecord="isViewingAnOrder = $event">
                </invoiceTableWidget>

            </Col>

        </Row>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue'; 
    
    /*  Active Order Widget  */
    import showActiveOrderWidget from './../show/main.vue';

    /*  Local Widgets  */
    import invoiceTableWidget from './table.vue'

    export default {
        props: {
            ordersUrl: {
                type: String,
                default: null
            },
            store: {
                type: Object,
                default: null
            }
        }, 
        components: { 
            basicButton, showActiveOrderWidget, invoiceTableWidget
        },
        data(){
            return {
                //  Current store orders
                localOrders: [],
                isViewingAnOrder: false
            }
        },
        watch: {

            /** Watch for changes on the isViewingAnOrder to know if the user is currently viewing a 
             *  specific order. If the user is veiwing a specific order we need to notify the parent
             *  component to hide the store menu so that it is not present. If the user exist out of
             *  viewing a specific order and returns to the order list we must notify the parent to
             *  show the store menu again.
             */ 
            isViewingAnOrder: {
                handler: function (val, oldVal) {

                    this.$emit('hideStoreMenu', val);

                }
            }

        }
    };
  
</script>