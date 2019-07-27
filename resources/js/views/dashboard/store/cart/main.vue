<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            
            <!-- Get the store details -->
            <cartWidget cartType="table-cart"></cartWidget>
            
        </Col>

    </Row>

</template>

<script> 

    /*  Widgets   */
    import cartWidget from './../../../../widgets/store/cart/main.vue';


    export default {
        components: { cartWidget },
        data(){
            return {

            }
        }
    };
</script>