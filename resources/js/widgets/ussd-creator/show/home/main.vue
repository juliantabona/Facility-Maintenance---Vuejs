<template>

  <!-- 
    Creator Home
  -->
  <div>

    <Row :gutter="30" class="mt-3 mr-0 ml-0">

        <Col :span="8" class="mt-0 mb-0 pl-0">
            <Card style="box-shadow: 3px 5px 5px #cacaca;">

                <!-- Main Heading -->  
                <Divider orientation="left">
                    <span class="text-primary">Creator Details</span>
                </Divider>

                <template v-if="updateCreatorUrl">
                    
                    <!-- Show edit creator form -->
                    <editCreatorForm 
                        :creator="localUssdCreator"
                        :postURL="updateCreatorUrl"
                        @updateSuccess="handleUpdateSuccess($event)">
                    </editCreatorForm>

                </template>

            </Card>
        </Col>

        <Col :span="8" class="mt-0 mb-0 pl-0">

            <Card>

                <span class="d-block font-weight-bold mt-2 heading" style="font-size: 18px;">Duplicate Creator</span>
                <span class="d-block text-justify mt-2">Do you want to duplicate this creator and its resources? Once a creator is duplicated you can edit it and its resources.</span>

                <div class="clearfix">

                    <!-- Duplicate Creator Button  -->
                    <Button type="primary" size="large" class="float-right mt-3">
                        <Icon type="ios-copy-outline" class="mr-1" size="20"/>
                        <span>Duplicate</span>
                    </Button>

                </div>

            </Card>

        </Col>

        <Col :span="8" class="mt-0 mb-0 pl-0">

            <Card>

                <span class="d-block font-weight-bold mt-2 heading" style="font-size: 18px;">Delete Creator</span>
                <span class="d-block text-justify mt-2">Do you want to delete this creator and all of its billing, subcriptions and customers? Note that once a creator is deleted you cannot retrieve again.</span>

                <div class="clearfix">

                    <!-- Delete Creator Button  -->
                    <Poptip confirm title="Are you sure you want to delete this creator?" class="float-right mt-3"
                            ok-text="Yes" cancel-text="No" width="300" @on-ok="deleteCreator(index)"
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

    /*  Edit Creator Form  */
    import editCreatorForm from './../../../../components/_common/forms/edit-creator/editCreator.vue';

    export default {
        props: {
            ussdCreator: {
                type: Object,
                default: null
            }
        },
        components:{ editCreatorForm },
        data(){
            return {
                localUssdCreator: this.ussdCreator
            }
        },
        computed: {
            updateCreatorUrl(){

                return this.localUssdCreator['_links']['self']['href'];

            }
        },
        methods: {
            handleUpdateSuccess(updatedCreator){
    
                //  Notify the parent and pass the data
                this.$emit('updatedCreator', updatedCreator);

            },
            duplicateCreator(){

            },
            deleteCreator(){

            }
        },
        created(){

        }
    };
  
</script>