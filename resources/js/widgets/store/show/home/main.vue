<template>

  <!-- 
    Store Home
  -->
  <div>

    <Row :gutter="30" class="mt-3 mr-0 ml-0">

        <Col :span="8" class="mt-0 mb-0 pl-0">
            <Card style="box-shadow: 3px 5px 5px #cacaca;">

                <!-- Main Heading -->  
                <Divider orientation="left">
                    <span class="text-primary">Store Details</span>
                </Divider>

                <template v-if="updateStoreUrl">
                    
                    <!-- Show edit store form -->
                    <editStoreForm 
                        :store="localStore"
                        :postURL="updateStoreUrl"
                        @updateSuccess="handleUpdateSuccess($event)">
                    </editStoreForm>

                </template>

            </Card>
        </Col>

        <Col :span="8" class="mt-0 mb-0 pl-0">

            <Card>

                <span class="d-block font-weight-bold mt-2 heading" style="font-size: 18px;">Duplicate Store</span>
                <span class="d-block text-justify mt-2">Do you want to duplicate this store and its products? Once a store is duplicated you can edit it and its products.</span>

                <div class="clearfix">

                    <!-- Duplicate Store Button  -->
                    <Button type="primary" size="large" class="float-right mt-3">
                        <Icon type="ios-copy-outline" class="mr-1" size="20"/>
                        <span>Duplicate</span>
                    </Button>

                </div>

            </Card>

        </Col>

        <Col :span="8" class="mt-0 mb-0 pl-0">

            <Card>

                <span class="d-block font-weight-bold mt-2 heading" style="font-size: 18px;">Delete Store</span>
                <span class="d-block text-justify mt-2">Do you want to delete this store and all of its orders, products and customers? Note that once a store is deleted you cannot retrieve again.</span>

                <div class="clearfix">

                    <!-- Delete Store Button  -->
                    <Poptip confirm title="Are you sure you want to delete this store?" class="float-right mt-3"
                            ok-text="Yes" cancel-text="No" width="300" @on-ok="deleteStore(index)"
                            placement="top-end">
                        <Button type="error" size="large">
                            <Icon type="ios-trash-outline" class="mr-1" size="20"/>
                            <span>Delete</span>
                        </Button>
                    </Poptip>
                </div>

            </Card>

        </Col>

    </Row>
    
  </div>

</template>

<script>

    /*  Edit Store Form  */
    import editStoreForm from './../../../../components/_common/forms/edit-store/editStore.vue';

    export default {
        props: {
            store: {
                type: Object,
                default: null
            }
        },
        components:{ editStoreForm },
        data(){
            return {
                localStore: this.store
            }
        },
        computed: {
            updateStoreUrl(){

                return this.localStore['_links']['self']['href'];

            }
        },
        methods: {
            handleUpdateSuccess(updatedStore){
    
                //  Notify the parent and pass the data
                this.$emit('updatedStore', updatedStore);

            },
            duplicateStore(){

            },
            deleteStore(){

            }
        },
        created(){

        }
    };
  
</script>