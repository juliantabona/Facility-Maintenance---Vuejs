<template>

    <Row :gutter="4">

        <Col :span="24">

            <!-- Test Event API Instructions -->
            <Alert v-if="!isTestingApiUrl && !testAPIResponse" 
                    type="info" style="line-height: 1.4em;" class="mb-2" closable>
                Enter the <span class="font-italic text-success font-weight-bold">Request URL</span> then
                hit <span class="font-weight-bold text-success">Test API</span> to get a response.
            </Alert>

        </Col>

        <Col :span="18">

            <i-input v-model="localEvent.event_data.url" class="w-100 mb-2" placeholder="https://www.website.com/api/items">
                
                <!-- Select Event API Method -->
                <Select v-model="localEvent.event_data.method" slot="prepend" style="width: 80px">
                    <Option value="get">GET</Option>
                    <Option value="post">POST</Option>
                    <Option value="patch">PATCH</Option>
                    <Option value="delete">DELETE</Option>
                </Select>

            </i-input>

        </Col>

        <Col :span="6">

            <!-- Test Event API Button -->
            <Button class="float-right" type="success" @click.native="testApi()"
                :disabled="isTestingApiUrl || !localEvent.event_data.url" >
                <Icon type="ios-repeat" :size="20" />
                <span>Test Api</span>
            </Button>

        </Col>

        <Col :span="24" class="mt-2">

            <!-- Test API loader -->
            <Loader v-if="isTestingApiUrl" :loading="isTestingApiUrl" type="text" class="text-left" theme="white">Requesting...</Loader>

            <template v-if="testAPIResponse">

                <!-- Test API Response Navigation Tabs -->
                <Tabs v-model="activeNavTab" type="card" style="overflow: visible;" :animated="false">

                    <!-- Test API Response Tab -->
                    <TabPane v-for="(currentTabName, key) in navTabs" :key="key" :label="currentTabName" :name="currentTabName"></TabPane>

                </Tabs>
                
                <!-- Test API Response Data --> 
                <div class="bg-grey-light border p-2">

                    <span v-if="testAPIResponse.status" class="d-inline-block mr-4">
                        <span class="font-weight-bold text-dark">Status: </span>
                        <span :class="(testAPIResponse.status == '200' ? 'text-success' : 'text-danger')">
                            {{ testAPIResponse.status }}
                        </span>
                    </span>

                    <span v-if="testAPIResponse.statusText" class="d-inline-block">
                        <span class="font-weight-bold text-dark">Status Text: </span>
                        <span>{{ testAPIResponse.statusText }}</span>
                    </span>

                </div>

                <!-- Test API Response Body --> 
                <template v-if="activeNavTab == 'Body'">
                    
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

                <!-- Test API Response Headers --> 
                <template v-if="activeNavTab == 'Headers'">

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

            </template>

        </Col>

    </Row>

    
</template>

<script>

    //  Get the loader
    import Loader from './../../../../../../../../../components/_common/loaders/Loader.vue';

    export default {
        props:{
            event: {
                type: Object,
                default: null
            }
        },
        components: { Loader },
        data(){
            return{
                localEvent: this.event,
                isTestingApiUrl: false,
                testAPIResponse: null,
                activeNavTab: 'Body'
            }
        },
        computed: {

            navTabs(){
                var tabs = ['Body', 'Headers'];

                return tabs;
            },

            testAPIResponsePrettierFormat(){

                return JSON.stringify( (this.testAPIResponse || {}).data, undefined, 2);

            }

        },
        methods: {
            testApi(){
                
                //  If we have the event request data and method
                if(this.localEvent.event_data.method && this.localEvent.event_data.url){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    this.isTestingApiUrl = true;

                    //  Reset the API test data and error log
                    this.testAPIResponse = null;

                    //  Use the api call() function located in resources/js/api.js
                    api.call(this.localEvent.event_data.method, this.localEvent.event_data.url)
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
        }
    };
  
</script>