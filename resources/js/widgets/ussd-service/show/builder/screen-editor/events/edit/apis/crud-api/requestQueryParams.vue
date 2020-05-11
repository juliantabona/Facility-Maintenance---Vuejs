<template>

    <div>

        <!-- Query Params Instructions -->
        <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
            Use <span class="font-italic text-success font-weight-bold">Query Params</span> to append additional data
            that must be sent along with your API Request e.g adding a query that limits the response
            payload.
        </Alert>

        <Row :gutter="4">

            <Col :span="11">
        
                <span class="d-block font-weight-bold text-dark mb-2">Key</span>

            </Col>

            <Col :span="13">
        
                <span class="d-block font-weight-bold text-dark mb-2">Value</span>

            </Col>

        </Row>

        <template v-if="queryParamsExist">

            <Row v-for="(query_param, index) in localEvent.event_data.query_params" :key="index" :gutter="4" class="mb-2">

                <Col :span="11">

                    <i-input v-model="query_param.key" size="small" class="w-100" placeholder="limit"></i-input>

                </Col>

                <Col :span="11">

                    <i-input v-model="query_param.value" size="small" class="w-100" placeholder="10"></i-input>

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

        <!-- No Query Params message -->
        <Alert v-else type="info" class="mb-2" show-icon>No Query Params Found</Alert>

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

            //  Check if the query params exist
            queryParamsExist(){

                return (this.localEvent.event_data.query_params.length) ? true : false ;

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
                this.localEvent.event_data.query_params.push( template );

            },
            removeOption(index){

                //  Remove option 
                this.localEvent.event_data.query_params.splice(index, 1);

            }

        }
    };
  
</script>