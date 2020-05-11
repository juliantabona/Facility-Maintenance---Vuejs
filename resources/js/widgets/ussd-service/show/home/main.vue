<template>

  <!-- 
    Ussd Service Home
  -->
  <div>

    <Row :gutter="30" class="mt-3 mr-0 ml-0">

        <Col :span="8" class="mt-0 mb-0 pl-0">
            <Card style="box-shadow: 3px 5px 5px #cacaca;">

                <!-- Main Heading -->  
                <Divider orientation="left">
                    <span class="text-primary">Service Details</span>
                </Divider>

                <template v-if="updateUssdServiceUrl">
                    
                    <!-- Show edit Ussd Service form -->
                    <editUssdServiceForm 
                        :ussdService="localUssdService"
                        :postURL="updateUssdServiceUrl"
                        @updateSuccess="handleUpdateSuccess($event)">
                    </editUssdServiceForm>

                </template>

            </Card>
        </Col>

        <Col :span="8" class="mt-0 mb-0 pl-0">

            <Card>

                <span class="d-block font-weight-bold mt-2 heading" style="font-size: 18px;">Duplicate Service</span>
                <span class="d-block text-justify mt-2">Do you want to duplicate this ussd service and its resources? Once the service is duplicated you can edit it and its resources.</span>

                <div class="clearfix">

                    <!-- Duplicate Ussd Service Button  -->
                    <Button type="primary" size="large" class="float-right mt-3">
                        <Icon type="ios-copy-outline" class="mr-1" size="20"/>
                        <span>Duplicate</span>
                    </Button>

                </div>

            </Card>

        </Col>

        <Col :span="8" class="mt-0 mb-0 pl-0">

            <Card>

                <span class="d-block font-weight-bold mt-2 heading" style="font-size: 18px;">Delete Service</span>
                <span class="d-block text-justify mt-2">Do you want to delete this ussd service and all of its billing, subcriptions and customers? Note that once this service is deleted you cannot retrieve again.</span>

                <div class="clearfix">

                    <!-- Delete Ussd Service Button  -->
                    <Poptip confirm title="Are you sure you want to delete this ussd service?" class="float-right mt-3"
                            ok-text="Yes" cancel-text="No" width="300" @on-ok="deleteUssdService(index)"
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

    /*  Edit Ussd Service Form  */
    import editUssdServiceForm from './../../../../components/_common/forms/edit-ussd-service/editUssdService.vue';

    export default {
        props: {
            ussdService: {
                type: Object,
                default: null
            }
        },
        components:{ editUssdServiceForm },
        data(){
            return {
                localUssdService: this.ussdService
            }
        },
        computed: {
            updateUssdServiceUrl(){

                return this.localUssdService['_links']['self']['href'];

            }
        },
        methods: {
            handleUpdateSuccess(updatedUssdService){
    
                //  Notify the parent and pass the data
                this.$emit('updatedUssdService', updatedUssdService);

            },
            duplicateUssdService(){

            },
            deleteUssdService(){

            }
        },
        created(){

        }
    };
  
</script>