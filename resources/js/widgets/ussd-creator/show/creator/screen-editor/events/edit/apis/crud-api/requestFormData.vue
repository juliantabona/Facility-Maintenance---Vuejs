<template>

    <div>

        <!-- Form Data Instructions -->
        <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
            Use <span class="font-italic text-success font-weight-bold">Form Data</span> along with 
            request methods such as POST or PUT in order to append additional data that must be sent
            along with your API Request e.g adding data for an object that must be created or updated.
        </Alert>

        <Row :gutter="4">

            <Col :span="11">
        
                <span class="d-block font-weight-bold text-dark mb-2">Key</span>

            </Col>

            <Col :span="13">
        
                <span class="d-block font-weight-bold text-dark mb-2">Value</span>

            </Col>

        </Row>

        <template v-if="formDataExist">

            <Row v-for="(form_data_item, index) in localEvent.event_data.form_data" :key="index" :gutter="4" class="mb-2">

                <Col :span="11">

                    <i-input v-model="form_data_item.key" size="small" class="w-100" placeholder="limit"></i-input>

                </Col>

                <Col :span="11">

                    <i-input v-model="form_data_item.value" size="small" class="w-100" placeholder="10"></i-input>

                </Col>

                <Col :span="2" class="clearfix">

                    <!-- Remove Option Button  -->
                    <Poptip confirm title="Are you sure you want to remove this option?" 
                            ok-text="Yes" cancel-text="No" width="300" @on-ok="removeOption(index)"
                            placement="top-end" class="float-right">
                        <Icon type="ios-trash-outline" size="20"/>
                    </Poptip>

                </Col>

            </Row>

        </template>

        <!-- No Form Data message -->
        <Alert v-else type="info" class="mb-2" show-icon>No Form Data Found</Alert>

        <div class="clearfix">

            <!-- Add Button -->
            <Button class="float-right" @click.native="addOption()">
                <Icon type="ios-add" :size="20" />
                <span>Add</span>
            </Button>

        </div>

    </div>
    
</template>

<script>

    export default {
        props:{
            event: {
                type: Object,
                default: null
            }
        },
        data(){
            return{

                localEvent: this.event
                
            }
        },
        computed: {

            //  Check if the form data exist
            formDataExist(){

                return (this.localEvent.event_data.form_data.length) ? true : false ;

            }

        },
        methods: {

            addOption(){
                
                //  Build the option template
                var template = {
                        name: '',
                        value: ''
                    };

                //  Add new option  
                this.localEvent.event_data.form_data.push( template );

            },

            removeOption(index){

                //  Remove option 
                this.localEvent.event_data.form_data.splice(index, 1);

            }

        }
    };
  
</script>