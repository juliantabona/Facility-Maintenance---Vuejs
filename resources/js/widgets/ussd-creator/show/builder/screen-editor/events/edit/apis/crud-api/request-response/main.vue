<template>

    <div>

        <!-- Header -->
        <span class="d-block font-weight-bold text-dark">Responses</span>

        <!-- General Details i.e Default Messages -->
        <div class="bg-grey-light border mt-2 mb-3 p-2">

            <div>
                <!-- Heading -->
                <span class="d-block text-dark float-left">
                    <span class="font-weight-bold">Success </span>(Default Message)
                </span>

                <!-- Default API Error -->
                <el-input type="text" v-model="localEvent.event_data.response.general.default_success_message" size="small" class="w-100 mb-3"></el-input>
            </div>

            <div>
                <!-- Heading -->
                <span class="d-block text-dark float-left">
                    <span class="font-weight-bold">Error </span>(Default Message)
                </span>

                <!-- Default API Error -->
                <el-input type="text" v-model="localEvent.event_data.response.general.default_error_message" size="small" class="w-100 mb-3"></el-input>
            </div>

        </div>

        <!-- Select response type i.e Automatic / Manual -->
        <div class="d-flex mt-2">

            <span class="font-weight-bold text-dark mt-1 mr-2">Use:</span>
            <Select v-model="localEvent.event_data.response.selected_type" 
                    placeholder="Select" class="mb-4">

                <Option 
                    v-for="(api_response_type, key) in apiResponseTypes"
                    :key="key" :value="api_response_type.value" 
                    :label="api_response_type.name">
                </Option>

            </Select>

        </div>

        <!-- Manual response -->
        <template v-if="localEvent.event_data.response.selected_type == 'manual'">

            <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
                Add one or more <span class="font-italic text-success font-weight-bold">Status Codes</span> to take advantage of dynamic screen content. 
                Each status code represents an opportunity to handle the request in a specific manner e.g fetching the response data and displaying it
                as dynamic <span class="font-italic text-success font-weight-bold">Select Options</span>  on a status
                <span class="font-italic text-success font-weight-bold">200</span>, or displaying the 
                <span class="font-italic text-success font-weight-bold">Api Error</span> to the user on a status
                <span class="font-italic text-danger font-weight-bold">400</span>.
            </Alert>

            <div class="clearfix">

                <!-- Add Status Code Button -->
                <Button type="primary" class="float-right" @click.native="addStatusCodeHandle()">
                    <Icon type="ios-add" :size="20" />
                    <span>Add Status Code</span>
                </Button>

            </div>

             <template v-if="responseStatusHandlesExist">

                <Tabs v-model="activeNavTab" :key="renderKey" type="card" style="overflow: visible;" 
                     :animated="false" closable @on-tab-remove="handleTabRemoved($event)">

                    <!-- Response Status Code Navigation Tabs -->
                    <TabPane v-for="(responseStatusHandle, key) in localEvent.event_data.response.manual.response_status_handles" 
                             :key="key" :label="responseStatusHandle.status" :name="responseStatusHandle.status">

                        <!-- Response Attributes -->
                        <div class="bg-grey-light border mt-3 mb-3 p-2">

                            <Row :gutter="4">

                                <Col :span="11">
                            
                                    <span class="d-block font-weight-bold text-dark mb-2">Attribute Name</span>

                                </Col>

                                <Col :span="13">
                            
                                    <span class="d-block font-weight-bold text-dark mb-2">Response Target</span>

                                </Col>

                            </Row>

                            <Row v-for="(attribute, index) in responseStatusHandle.attributes" :key="index" :gutter="4" class="mb-2">

                                <Col :span="11">

                                    <i-input v-model="attribute.name" size="small" class="w-100" 
                                            :placeholder="getAttributePlaceholder(responseStatusHandle.status, index, 0)">
                                        <div slot="prepend">@</div>
                                    </i-input>

                                </Col>

                                <Col :span="11">

                                    <i-input v-model="attribute.value" size="small" class="w-100" 
                                            :placeholder="getAttributePlaceholder(responseStatusHandle.status, index, 1)" 
                                            :disabled="index == 0">
                                    </i-input>

                                </Col>

                                <Col v-if="index != 0" :span="2" class="clearfix">

                                    <!-- Remove Option Button  -->
                                    <Poptip confirm title="Are you sure you want to remove this attribute?" 
                                            ok-text="Yes" cancel-text="No" width="300" @on-ok="removeResponseOption(responseStatusHandle, index)"
                                            placement="top-end" class="float-right">
                                        <Icon type="ios-trash-outline" size="20"/>
                                    </Poptip>

                                </Col>

                            </Row>

                            <div class="clearfix">

                                <!-- Run Api -->
                                <Button class="float-right" @click.native="addResponseOption(responseStatusHandle)">
                                    <Icon type="ios-add" :size="20" />
                                    <span>Add</span>
                                </Button>

                            </div>

                        </div>

                        <!-- On Handle -->
                        <div class="d-flex mb-3">
                            <span class="font-weight-bold text-dark mt-1 mr-2">After Response:</span>
                            <Select v-model="responseStatusHandle.on_handle.selected_type" style="width: 200px;">

                                <Option 
                                    v-for="(option, key) in manualOptions"
                                    :key="key" :value="option.value" :label="option.name">
                                </Option>

                            </Select>
                        </div>

                        <customMessage v-if="responseStatusHandle.on_handle.selected_type == 'use_custom_msg'" 
                            :responseStatusHandle="responseStatusHandle">
                        </customMessage>

                    </TabPane>

                </Tabs>
                
            </template>

            <!-- Add status code handle message -->
            <Alert v-else type="info" class="mt-2 mb-2" show-icon>Add status codes to handle</Alert>

        </template>

        <template v-else>

            <!-- Automatic response -->
            <Alert type="info" style="line-height: 1.4em;" class="mb-2">
                The application will automatically decide whether to use the <span class="font-italic text-success font-weight-bold">default success</span> or
                <span class="font-italic text-success font-weight-bold">default error</span> message depending on whether or not the API call is successful.
            </Alert>

            <Row :gutter="10">

                <!-- On Success Handle -->
                <Col :span="12" class="d-flex">
                    <span class="font-weight-bold text-dark mt-1 mr-2">On Success:</span>
                    <Select v-model="localEvent.event_data.response.automatic.on_handle_success" style="width: 200px;">

                        <Option 
                            v-for="(option, key) in automaticSuccessOptions"
                            :key="key" :value="option.value" :label="option.name">
                        </Option>

                    </Select>
                </Col>

                <!-- On Fail Handle -->
                <Col :span="12" class="d-flex">
                    <span class="font-weight-bold text-dark mt-1 mr-2">On Fail:</span>
                    <Select v-model="localEvent.event_data.response.automatic.on_handle_error" style="width: 200px;">

                        <Option 
                            v-for="(option, key) in automaticErrorOptions"
                            :key="key" :value="option.value" :label="option.name">
                        </Option>

                    </Select>
                </Col>

            </Row>

        </template>
    
        <!-- 
            MODAL TO ADD NEW STATUS CODE HANDLE
        -->
        <createStatusCodeHandleModal v-if="isOpenCreateStatusCodeHandle" 
            @visibility="isOpenCreateStatusCodeHandle = $event"
            @createdStatusHandle="handleCreatedStatusHandle($event)">
        </createStatusCodeHandleModal> 

    </div>

</template>

<script>

    //  Get the display editor
    import customMessage from './customMessage.vue';

    //  Get the create new status code handle modal
    import createStatusCodeHandleModal from './createStatusCodeHandleModal';

    export default {
        props:{
            event: {
                type: Object,
                default: null
            }
        },
        components: {
            customMessage, createStatusCodeHandleModal
        },
        data(){
            return{
                renderKey: 1,
                localEvent: this.event,
                isOpenCreateStatusCodeHandle: false,
                activeNavTab: this.getFirstTabStatus(),
                automaticSuccessOptions: [
                    { 
                        name: 'Display Default Success Message',
                        value: 'use_default_success_msg'
                    },
                    { 
                        name: 'Do Nothing',
                        value: 'do_nothing'
                    }
                ],
                automaticErrorOptions: [
                    { 
                        name: 'Display Default Error Message',
                        value: 'use_default_error_msg'
                    },
                    { 
                        name: 'Do Nothing',
                        value: 'do_nothing'
                    }
                ],
                manualOptions: [
                    { 
                        name: 'Display Custom Message',
                        value: 'use_custom_msg'
                    },
                    { 
                        name: 'Do Nothing',
                        value: 'do_nothing'
                    }
                ],
                apiResponseTypes: [
                    { 
                        name: 'Automatic Responses',
                        value: 'automatic'
                    },
                    { 
                        name: 'Manual Responses',
                        value: 'manual'
                    }
                ]
            }
        },

        computed: {

            //  Check if the response status handles exist
            responseStatusHandlesExist(){

                return (this.localEvent.event_data.response.manual.response_status_handles.length) ? true : false ;

            }

        },

        methods: {
            getAttributePlaceholder(status, index, value){

                var isGoodStatus = ['1', '2', '3'].includes(status.substring(0, 1)) ? true : false;

                if(isGoodStatus){

                    var examples = [
                        ['products_response', 'response'],
                        ['products', '{{ response.products }}'],
                        ['total_products', '{{ response.total }}']
                    ];

                }else{
                    
                    var examples = [
                        ['error_response', 'response'],
                        ['error_message', '{{ response.error.message }}']
                    ];

                }

                if( (index + 1) <= examples.length ){
                    
                    return examples[index][value];

                }

                return '';
            },
            handleTabRemoved(statusType){

                var response_status_handles = this.localEvent.event_data.response.manual.response_status_handles;
                
                for(var x=0; x < response_status_handles.length; x++){

                    if( response_status_handles[x].status == statusType){

                        //  Remove the response status handle 
                        this.localEvent.event_data.response.manual.response_status_handles.splice(x, 1);

                        break;

                    }

                }

                this.activeNavTab = this.getFirstTabStatus();

                this.forceRenderTabs();

            },
            forceRenderTabs(){
                this.renderKey = ++this.renderKey;
            },
            addStatusCodeHandle(){
                this.isOpenCreateStatusCodeHandle = true;
            },
            getFirstTabStatus(){

                if( this.responseStatusHandlesExist ){

                    return this.localEvent.event_data.response.manual.response_status_handles[0].status;

                }

                return null;
            },
            handleCreatedStatusHandle( statusCodeHandle ){

                if( statusCodeHandle ){

                    //  Get the existing status code handles
                    var response_status_handles = this.localEvent.event_data.response.manual.response_status_handles;
                    
                    //  Make sure the new handle does not conflict with any other handles
                    for(var x=0; x < response_status_handles.length; x++){

                        if( response_status_handles[x].status == statusCodeHandle.status ){

                            //  Stop - This status code handle already exists
                            return null;

                        }

                    }

                    //  Add the new status code handle
                    this.localEvent.event_data.response.manual.response_status_handles.push( statusCodeHandle );

                    //  Set the active navigation tab to the current status code handle
                    this.activeNavTab = statusCodeHandle.status;

                }

            },
            addResponseOption(responseStatusHandle){
                
                //  Build the option template
                var template = {
                        name: '',
                        value: ''
                    };

                //  Add the template to the current response status handle attributes
                responseStatusHandle.attributes.push( template );

            },
            removeResponseOption(responseStatusHandle, index){

                //  Remove option 
                responseStatusHandle.attributes.splice(index, 1);

            }

        }
    };
  
</script>