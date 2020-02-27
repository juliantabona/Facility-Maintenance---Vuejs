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

            <el-input v-if="showContent" type="text" v-model="localRequest.name" size="small" class="w-50"></el-input>

            <!-- Request Name  -->
            <span v-else class="request-name font-weight-bold cut-text">
                {{ getRequestNumber ? getRequestNumber +'. ' : '' }}
                {{ localRequest.name }}
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
                                    
                <div class="clearfix">

                    <!-- API URI -->
                    <span class="d-block font-weight-bold text-dark">API URL</span>
                        
                    <Row :gutter="20">

                        <Col :span="18">

                            <i-input v-model="localRequest.url" class="w-100 mb-2" placeholder="https://www.website.com/api/products">
                                
                                <!-- Select API Method -->
                                <Select v-model="localRequest.method" slot="prepend" style="width: 80px">
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
                                :disabled="isTestingApiUrl || !localRequest.url" >
                                <Icon type="ios-repeat" :size="20" />
                                <span>Test Api</span>
                            </Button>

                        </Col>

                    </Row>

                </div>

                <Tabs type="card" :animated="false" v-model="selectedApiResponseTab" class="mt-3">

                    <!-- API Response For Status 200 -->
                    <TabPane v-for="(responseType, key) in api_response_types" :key="key"
                            :label="responseType.status" :name="responseType.status">

                        <span class="d-block mb-2">
                            <span class="font-weight-bold text-dark">Status: </span>
                            <span :class="( responseType.status == '200' ? 'text-success' : 'text-danger')">{{ responseType.status }}</span>
                        </span>

                        <span v-if="(testAPIResponse || {}).status == responseType.status" class="d-block mb-2">
                            <span class="font-weight-bold text-dark">Status Text: </span>
                            <span>{{ (testAPIResponse || {}).statusText }}</span>
                        </span>

                        <span v-if="responseType.description" class="d-block mb-2">
                            <span class="font-weight-bold text-dark">Description: </span>

                            <!-- Use the dynamic Api status description otherwise default to the static dscription --> 
                            <span>{{ responseType.description }}</span>
                        </span>

                        <!-- API Response Data -->
                        <template v-if="(testAPIResponse || {}).status == responseType.status">
                            
                            <div v-if="testAPIResponse" class="border mt-3">

                                <div class="clearfix bg-grey-light border-bottom p-2">
                                    <span class="d-block font-weight-bold text-dark float-left">Response Data</span>
                                    <Badge :text="responseType.status" class="float-right"
                                            :status="( responseType.status == '200' ? 'success' : 'error')">
                                    </Badge>
                                </div>

                                <div class="p-2" style="max-height:200px;overflow:auto;">
                                    
                                    <pre style="width: fit-content;">{{ testAPIResponsePrettierFormat }}</pre>

                                </div>
                            </div>

                        </template>

                        <!-- Show API loader -->
                        <Loader :loading="isTestingApiUrl" type="text" class="mt-2 text-left" theme="white">Requesting...</Loader>

                        <div class="mt-3 mb-3 pt-3 border-top">

                            <Row :gutter="4">

                                <Col :span="12">
                            
                                    <span class="d-block font-weight-bold text-dark mb-2">Response Name</span>

                                </Col>

                                <Col :span="12">
                            
                                    <span class="d-block font-weight-bold text-dark mb-2">Response Value</span>

                                </Col>

                            </Row>

                            <Row v-for="(attribute, key) in getScreenAttributes" :key="key" :gutter="4" class="mb-2">

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

                            <template v-if="getScreenContent">
                               
                                <screenContentWidget :screenContent="getScreenContent" :screenTree="screenTree" class="mb-5"></screenContentWidget>
                            
                            </template>

                        </div>

                    </TabPane>

                </Tabs>
            
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
                selectedApiResponseTab: '200',
                api_response_types: [
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
            getScreenAttributes(){
                
                for(var x=0; x < this.localRequest.response_data.length; x++){

                    if( this.localRequest.response_data[x].status == this.selectedApiResponseTab){

                        return this.localRequest.response_data[x].attributes;

                    }

                }

                return [];
            },
            getScreenContent(){
                
                for(var x=0; x < this.localRequest.response_data.length; x++){

                    if( this.localRequest.response_data[x].status == this.selectedApiResponseTab){

                        return this.localRequest.response_data[x].content;

                    }

                }

                return null;
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

                //  Foreach response
                for(var x=0; x < this.localRequest.response_data.length; x++){

                    //  If the current response data status is equal to the current open tab
                    if( this.localRequest.response_data[x].status == parseInt(this.selectedApiResponseTab) ){
                        
                        //  Add the template
                        this.localRequest.response_data[x].attributes.push( template );

                        //  Stop loop
                        break;

                    }

                }
            },
            testApi(){
                
                if(this.localRequest.method && this.localRequest.url){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    this.isTestingApiUrl = true;

                    //  Reset the API test data and error log
                    this.testAPIResponse = null;

                    //  Use the api call() function located in resources/js/api.js
                    api.call(this.localRequest.method, this.localRequest.url)
                        .then((response) => {

                            //  Stop loader
                            self.isTestingApiUrl = false;
                            
                            self.testAPIResponse = response;

                            self.selectedApiResponseTab = ((response || {}).status || {}).toString();

                        })         
                        .catch(({response}) => {

                            //  Stop loader
                            self.isTestingApiUrl = false;
                            
                            self.testAPIResponse = response;

                            self.selectedApiResponseTab = ((response || {}).status || {}).toString();

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