<style scoped>

    .request-name {
        color: #515a6e;
    }

    .request-desc {
        color: #808695;
        font-weight: 100;
    }

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    .unbolded >>> .field-label{
        font-weight:100 !important;
    }

    /*  Request Toolbox */

    .single-request >>> .request-toolbox{
        margin: -2px 0 0 0;
    }

    .single-request:hover >>> .request-toolbox .hidable{
        opacity:1;
    }

    .single-request >>> .request-toolbox .hidable{
        opacity:0;
    }

    .single-request >>> .request-toolbox .request-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .single-request >>> .request-toolbox .request-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .single-request >>> .ivu-card-body{
        padding:0 !important;
    }

    .request-details{
        padding:16px;
    }

</style>

<template>

    <Card v-if="request" :class="'box-card single-request mb-2'+(!showContent ? ' hidden-content':'')">

        <div slot="title">

            <el-input v-if="showContent" type="text" v-model="localRequest.api_data.name" size="small" class="w-50"></el-input>

            <!-- Request Name  -->
            <span v-else class="request-name font-weight-bold cut-text">
                {{ getRequestNumber ? getRequestNumber +'. ' : '' }}
                {{ localRequest.api_data.name }}
            </span>
            
        </div>

        <div slot="extra">

            <div class="request-toolbox float-right d-block">

                <!-- Remove Request Button  -->
                <Poptip confirm title="Are you sure you want to remove this request?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveRequest(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="request-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Request Button  -->
                <Icon type="ios-create-outline" class="request-icon hidable" size="20" @click="showContent = !showContent" />

                <!-- Move Request Button  -->
                <Icon type="ios-move" class="request-icon request-dragger-handle hidable mr-2" size="20" />
            
            </div>
        </div>    

        <Row v-if="showContent" class="request-details">

            <Col :span="24">
                                    
                <div class="clearfix mb-3">

                    <Tabs v-model="selectedRequestTab" type="card" :animated="false">

                        <!-- API DETAILS-->
                        <TabPane v-for="(responseType, key) in ['Request Url', 'Query Params', 'Form Data', 'Headers', 'Responses']" 
                                :key="key" :label="responseType" :name="responseType">
                                    
                            <Row :gutter="20">

                                <!-- Query Params -->
                                <template v-if="responseType == 'Query Params'">

                                    <Col :span="24">

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

                                        <Row v-for="(query_param, index) in localRequest.api_data.query_params" :key="index" :gutter="4" class="mb-2">

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

                                        <div class="clearfix">

                                            <!-- Run Api -->
                                            <Button class="float-right" @click.native="addResponseOption()">
                                                <Icon type="ios-add" :size="20" />
                                                <span>Add</span>
                                            </Button>

                                        </div>

                                    </Col>

                                </template>

                                <!-- Form Data -->
                                <template v-else-if="responseType == 'Form Data'">

                                    <Col :span="24">

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

                                        <Row v-for="(form_data, index) in localRequest.api_data.form_data" :key="index" :gutter="4" class="mb-2">

                                            <Col :span="11">

                                                <i-input v-model="form_data.key" size="small" class="w-100" placeholder="item_id"></i-input>

                                            </Col>

                                            <Col :span="11">

                                                <i-input v-model="form_data.value" size="small" class="w-100" placeholder="10"></i-input>

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

                                        <div class="clearfix">

                                            <!-- Run Api -->
                                            <Button class="float-right" @click.native="addResponseOption()">
                                                <Icon type="ios-add" :size="20" />
                                                <span>Add</span>
                                            </Button>

                                        </div>

                                    </Col>

                                </template>

                                <!-- Headers -->
                                <template v-else-if="responseType == 'Headers'">

                                    <Col :span="24">

                                        <!-- Form Data Instructions -->
                                        <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
                                            Use <span class="font-italic text-success font-weight-bold">Headers</span> to modify 
                                            your request headers e.g using Content-Type = application/json to indicate the 
                                            resource's media type.
                                        </Alert>

                                        <Row :gutter="4">

                                            <Col :span="11">
                                        
                                                <span class="d-block font-weight-bold text-dark mb-2">Key</span>

                                            </Col>

                                            <Col :span="13">
                                        
                                                <span class="d-block font-weight-bold text-dark mb-2">Value</span>

                                            </Col>

                                        </Row>

                                        <Row v-for="(header, index) in localRequest.api_data.headers" :key="index" :gutter="4" class="mb-2">

                                            <Col :span="11">

                                                <i-input v-model="header.key" size="small" class="w-100" placeholder="Content-Type"></i-input>

                                            </Col>

                                            <Col :span="11">

                                                <i-input v-model="header.value" size="small" class="w-100" placeholder="application/json"></i-input>

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

                                        <div class="clearfix">

                                            <!-- Run Api -->
                                            <Button class="float-right" @click.native="addResponseOption()">
                                                <Icon type="ios-add" :size="20" />
                                                <span>Add</span>
                                            </Button>

                                        </div>

                                    </Col>

                                </template>

                            </Row>

                        </TabPane>

                    </Tabs>

                    <!-- Request URL -->
                    <template v-if="selectedRequestTab == 'Request Url'">
                        
                        <Row :gutter="20">
                            <Col :span="24">

                                <Row :gutter="4">

                                    <Col :span="24">

                                        <!-- Test API Instructions -->
                                        <Alert v-if="!isTestingApiUrl && !testAPIResponse" 
                                               type="info" style="line-height: 1.4em;" class="mb-2" closable>
                                            Enter the <span class="font-italic text-success font-weight-bold">Request URL</span> then
                                            hit <span class="font-weight-bold text-success">Test API</span> to get a response.
                                        </Alert>

                                    </Col>
                            
                                    <Col :span="18">

                                        <i-input v-model="localRequest.api_data.url" class="w-100 mb-2" placeholder="https://www.website.com/api/items">
                                            
                                            <!-- Select API Method -->
                                            <Select v-model="localRequest.api_data.method" slot="prepend" style="width: 80px">
                                                <Option value="get">GET</Option>
                                                <Option value="post">POST</Option>
                                                <Option value="patch">PATCH</Option>
                                                <Option value="delete">DELETE</Option>
                                            </Select>

                                        </i-input>

                                    </Col>

                                    <Col :span="6">

                                        <!-- Run Api -->
                                        <Button class="float-right" type="success" @click.native="testApi()"
                                            :disabled="isTestingApiUrl || !localRequest.api_data.url" >
                                            <Icon type="ios-repeat" :size="20" />
                                            <span>Test Api</span>
                                        </Button>

                                    </Col>

                                    <Col :span="24" class="mt-4">

                                        <!-- Test API loader -->
                                        <Loader v-if="isTestingApiUrl" :loading="isTestingApiUrl" type="text" class="text-left" theme="white">Requesting...</Loader>

                                        <!-- Test API Response Data -->
                                        <Tabs v-if="!isTestingApiUrl && testAPIResponse" v-model="selectedRequestUrlTab" type="card" :animated="false">

                                            <!-- API Response Details -->
                                            <TabPane v-for="(responseType, key) in ['Body', 'Headers']" 
                                                    :key="key" :label="responseType" :name="responseType">
                                                    
                                                <div class="bg-grey-light border p-2">
                                                    <span v-if="(testAPIResponse || {}).status" class="d-inline-block mr-4">
                                                        <span class="font-weight-bold text-dark">Status: </span>
                                                        <span :class="(testAPIResponse.status == '200' ? 'text-success' : 'text-danger')">
                                                            {{ testAPIResponse.status }}
                                                        </span>
                                                    </span>

                                                    <span v-if="(testAPIResponse || {}).statusText" class="d-inline-block">
                                                        <span class="font-weight-bold text-dark">Status Text: </span>
                                                        <span>{{ testAPIResponse.statusText }}</span>
                                                    </span>
                                                </div>

                                                <template v-if="responseType == 'Body'">
                                                    
                                                    <div class="mt-2">

                                                        <!-- API Response Data -->
                                                        <div v-if="(testAPIResponse || {}).status" class="border">

                                                            <div class="clearfix bg-grey-light border-bottom p-2">
                                                                <span class="d-block font-weight-bold text-dark float-left">Response Data</span>
                                                                <Badge :text="((testAPIResponse || {}).status || '').toString()" class="float-right"
                                                                        :status="( (testAPIResponse || {}).status == '200' ? 'success' : 'error')">
                                                                </Badge>
                                                            </div>

                                                            <div class="p-2" style="max-height:200px;overflow:auto;">
                                                                
                                                                <pre style="width: fit-content;">{{ testAPIResponsePrettierFormat }}</pre>

                                                            </div>
                                                        </div>
                                                        
                                                    </div>

                                                </template>

                                                <template v-if="responseType == 'Headers'">

                                                    <div class="p-2">
                                                        <template v-if="(testAPIResponse || {}).headers">
                                                            
                                                            <div v-for="(header_value, header_name) in (testAPIResponse || {}).headers" 
                                                                :key="header_name" class="mb-2">
                                                                <span class="font-weight-bold text-capitalize text-dark">{{ header_name }}: </span>
                                                                <span class="text-success text-break">{{ header_value }}</span>
                                                            </div>

                                                        </template>

                                                        <Divider class="d-block mt-2 mb-2" />

                                                        <template v-if="((testAPIResponse || {}).config || {}).headers">
                                                            
                                                            <div v-for="(header_value, header_name) in ((testAPIResponse || {}).config || {}).headers" 
                                                                :key="header_name" class="mb-2">
                                                                <span class="font-weight-bold text-capitalize text-dark">{{ header_name }}: </span>
                                                                <span class="text-success text-break">{{ header_value }}</span>
                                                            </div>

                                                        </template>
                                                    </div>

                                                </template>

                                            </TabPane>

                                        </Tabs>

                                    </Col>

                                </Row>

                            </Col>
                        </Row>

                    </template>

                    <!-- Responses -->
                    <template v-if="selectedRequestTab == 'Responses'">

                        <Row :gutter="20">
                            <Col :span="24">

                                <!-- Header -->
                                <span class="d-block font-weight-bold text-dark">Responses</span>

                                <!-- General Settings -->
                                <div class="bg-grey-light border mt-2 mb-3 p-2">

                                    <div>
                                        <!-- Heading -->
                                        <span class="d-block text-dark float-left">
                                            <span class="font-weight-bold">Success </span>(Default Message)
                                        </span>

                                        <!-- Default API Error -->
                                        <el-input type="text" v-model="localRequest.api_data.general.default_success_message" size="small" class="w-100 mb-3"></el-input>
                                    </div>

                                    <div>
                                        <!-- Heading -->
                                        <span class="d-block text-dark float-left">
                                            <span class="font-weight-bold">Error </span>(Default Message)
                                        </span>

                                        <!-- Default API Error -->
                                        <el-input type="text" v-model="localRequest.api_data.general.default_error_message" size="small" class="w-100 mb-3"></el-input>
                                    </div>

                                </div>

                                <!-- Select display mode -->
                                <div class="d-flex mt-2">
                                    <span class="font-weight-bold text-dark mt-1 mr-2">Use:</span>
                                    <Select v-model="localRequest.api_data.general.response_type" 
                                            placeholder="Select" class="mb-4">

                                        <Option 
                                            v-for="(api_response_type, key) in api_response_types"
                                            :key="key" :value="api_response_type.value" 
                                            :label="api_response_type.name">
                                        </Option>

                                    </Select>
                                </div>

                                <!-- Manual response -->
                                <template v-if="localRequest.api_data.general.response_type == 'manual'">

                                    <Alert type="info" style="line-height: 1.4em;" class="mb-2">
                                        Add one or more <span class="font-italic text-success font-weight-bold">Status Codes</span> to take advantage of dynamic screen content. 
                                        Each status code represents an opportunity to handle the request in a specific manner e.g fetching the response data and displaying it
                                        as dynamic <span class="font-italic text-success font-weight-bold">Select Options</span> or displaying the 
                                        <span class="font-italic text-success font-weight-bold">Api Error</span> to the user.
                                    </Alert>

                                    <Tabs v-model="selectedRequestResponseTab" type="card" :animated="false">

                                        <!-- API Response For Status 200 -->
                                        <TabPane v-for="(responseType, key) in localRequest.api_data.response_status_handles" :key="key"
                                                :label="responseType.status" :name="responseType.status"
                                                :closable="true">

                                            <span v-if="responseType.description" class="d-block mb-2">
                                                <span class="font-weight-bold text-dark">Description: </span>
                                                <span>{{ responseType.description }}</span>
                                            </span>

                                            <div class="bg-grey-light border mt-3 mb-3 p-2">

                                                <Row :gutter="4">

                                                    <Col :span="12">
                                                
                                                        <span class="d-block font-weight-bold text-dark mb-2">Response Name</span>

                                                    </Col>

                                                    <Col :span="12">
                                                
                                                        <span class="d-block font-weight-bold text-dark mb-2">Response Value</span>

                                                    </Col>

                                                </Row>

                                                <Row v-for="(attribute, key) in currentResponseHandle.attributes" :key="key" :gutter="4" class="mb-2">

                                                    <Col :span="12">

                                                        <i-input v-model="attribute.name" size="small" class="w-100" placeholder="products">
                                                            <div slot="prepend">@</div>
                                                        </i-input>

                                                    </Col>

                                                    <Col :span="12">

                                                        <i-input v-model="attribute.value" size="small" class="w-100" placeholder="response.products"
                                                                :disabled="key == 0"></i-input>

                                                    </Col>

                                                </Row>

                                                <div class="clearfix">

                                                    <!-- Run Api -->
                                                    <Button class="float-right" @click.native="addResponseOption()">
                                                        <Icon type="ios-add" :size="20" />
                                                        <span>Add</span>
                                                    </Button>

                                                </div>

                                            </div>

                                        </TabPane>

                                    </Tabs>

                                    <!-- If manual -->
                                    <div class="bg-grey-light border-bottom mt-2 p-2">
                                        <screenContentWidget 
                                            :screenTree="screenTree"
                                            :screenContent="currentResponseHandle.content">
                                        </screenContentWidget>
                                    </div>

                                </template>

                                <!-- Automatic response -->
                                <Alert v-else type="info" style="line-height: 1.4em;" class="mb-2">
                                    The application will automatically decide whether to use the <span class="font-italic text-success font-weight-bold">default success</span> or
                                    <span class="font-italic text-success font-weight-bold">default error</span> message depending on whether or not the API call is successful.
                                </Alert>

                            </Col>
                        </Row>

                    </template>

                </div>
            
            </Col>

        </Row>

    </Card>

</template>

<script>

    /*  Loaders  */
    import Loader from './../../../../components//_common/loaders/Loader.vue';

    import screenContentWidget from './screenContentWidget.vue';

    export default {
        components: { Loader, screenContentWidget },
        props:{
            index: {
                type: Number,
                default:null
            },
            request: {
                type: Object,
                default:() => {}
            },
            screenTree: {
                type: Array,
                default: function(){
                    return []
                }
            }
        }, 
        data(){
            return {
                localRequest: this.request,
                requestBeforeChange: null,
                isTestingApiUrl: false,
                testAPIResponse: null,
                showContent: false,
                selectedRequestTab: 'Request Url',
                selectedRequestUrlTab: 'Body',
                selectedRequestResponseTab: '200',
                api_response_types: [
                    { 
                        name: 'Automatic Responses',
                        value: 'automatic'
                    },
                    { 
                        name: 'Manual Responses',
                        value: 'manual'
                    },
                ],
                api_response_status_handles: [
                    { 
                        status: '200',
                        description: 'The request was handled successfully'
                    },
                    { 
                        status: '400',
                        description: 'The request was unsuccessful'
                    },
                    { 
                        status: '403',
                        description: 'The request was unauthorized'
                    },
                    { 
                        status: '404',
                        description: 'The request was unsuccessful'
                    },
                    { 
                        status: '405',
                        description: 'The request method is not allowed'
                    },
                    { 
                        status: '500',
                        description: 'Server error'
                    }
                ]
            }
        },

        watch: {
            /*  Keep track of changes on the request.  */
            request: {

                handler: function (val, oldVal) {

                    /*  Update the local request  */
                    this.localRequest = val;

                },
                deep: true

            },
        },
        computed: {
            getRequestNumber(){
                /**
                 *  Returns the request number. We use this as we list the requests.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            },
            checkIfRequestHasChanged(){
                var now = _.cloneDeep(this.localRequest);
                var before = (this.requestBeforeChange);
                var isNotEqual = !_.isEqual(now, before);

                return isNotEqual;
            },
            testAPIResponsePrettierFormat(){

                return JSON.stringify( (this.testAPIResponse || {}).data, undefined, 2);

            },
            currentResponseHandle(){
                
                for(var x=0; x < this.localRequest.api_data.response_status_handles.length; x++){

                    if( this.localRequest.api_data.response_status_handles[x].status == this.selectedRequestResponseTab){

                        return this.localRequest.api_data.response_status_handles[x];

                    }

                }

                return {};
            }
        },
        methods: {
            storeOriginalRequestData(){

                //  Store the original request data
                this.requestBeforeChange = _.cloneDeep(this.localRequest);

            },
            handleCancel(){

                //  Undo any changes made to the request while editing
                this.undoRequestChanges();

            },
            undoRequestChanges(){
                
                this.localRequest = _.cloneDeep(this.requestBeforeChange);

            },
            handleRemoveRequest(index) {

                this.$emit('removeRequest', index);

            },
            addResponseOption(){
                
                //  Build the option template
                var template = {
                        name: '',
                        value: ''
                    };

                //  Add the template to the current response status handle attributes
                this.currentResponseHandle.attributes.push( template );

            },
            testApi(){
                
                if(this.localRequest.api_data.method && this.localRequest.api_data.url){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    this.isTestingApiUrl = true;

                    //  Reset the API test data and error log
                    this.testAPIResponse = null;

                    //  Use the api call() function located in resources/js/api.js
                    api.call(this.localRequest.api_data.method, this.localRequest.api_data.url)
                        .then((response) => {

                            console.log(response);

                            //  Stop loader
                            self.isTestingApiUrl = false;
                            
                            self.testAPIResponse = response;

                            self.selectedRequestResponseTab = ((response || {}).status || {}).toString();

                        })         
                        .catch(({response}) => {

                            console.log(response);

                            //  Stop loader
                            self.isTestingApiUrl = false;
                            
                            self.testAPIResponse = response;

                            self.selectedRequestResponseTab = ((response || {}).status || {}).toString();

                        });

                }

            }
        },
        created(){

            //  Store the original request data before editing
            this.storeOriginalRequestData();

        }
    }

</script>