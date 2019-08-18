<template>

    <Row :gutter="20">

        <Col span="24">

            <!-- Get the store details -->
            <singleProductWidget></singleProductWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Widgets   */
    import singleProductWidget from './../../../../widgets/store/single-product/main.vue';


    export default {
        components: { 
          singleProductWidget
        },
        data(){
            return {

            }
        }
    };
</script>