<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :isSaving="false" 
            :hideModal="hideModal"
            title="Proof Of Payment"
            okText="" cancelText="Cancel"
            @on-ok="" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <Row v-if="isLoadingDocs" :gutter="20" class="pt-3 pb-3">
                    <Col :span="8" :offset="8">
                        <!-- Loader for when loading the docs -->
                        <Loader :loading="true" type="text" class="mt-2 mb-2">Loading...</Loader>
                    </Col>
                </Row>

                <!-- If we have documents to show -->
                <Row v-if="!isLoadingDocs && documents.length" :gutter="20" 
                     class="pt-3 pb-3" style="background:#4abf7f;">
                     
                    <Col :span="20" :offset="2">
                        
                        <!-- Receipts -->
                        <slick ref="slick" :options="slickOptions" class="mt-3 mb-3">
                            <div v-for="(doc, index) in documents" :key="index">

                                <!-- Receipt Info -->
                                <span class="d-block text-white text-center mt-3 mb-3">
                                    <span class="font-weight-bold">Uploaded Date: </span> 
                                    {{ doc.created_at | moment('DD MMM YYYY, H:mmA') || '___' }}
                                </span>

                                <img :src="doc.url" width="100%" class="pl-2 pr-2">

                            </div>
                        </slick>
                    </Col>

                    <!-- If we don't have documents to show -->
                    <Col v-if="!isLoadingDocs && !documents.length" :span="20" :offset="2">
                        <Alert type="warning" show-icon>Proof of payment hasn't been uploaded</Alert>

                        <basicButton class="mt-2 mb-2" :style="{ maxWidth: '250px', position:'relative' }"
                            type="info" size="small" :ripple="false"
                            @click.native="true">
                            <Icon type="ios-paper" :size="20" class="mr-1" style="margin-top: -4px;" />
                            <span>Remind Customer</span>
                        </basicButton>
                    </Col>
                </Row>

                <!-- If we don't have documents to show -->
                <Row v-if="!isLoadingDocs && !documents.length" :gutter="20" class="pt-3 pb-3">
                    <Col :span="20" :offset="2">
                        <Alert type="warning" show-icon>Customer hasn't uploaded any proof of payment</Alert>

                        <!-- Register Button -->
                        <basicButton class="mt-3 mb-2 pl-2 pr-3" type="success" size="large" 
                            :ripple="false" @click.native="true">
                            <Icon type="ios-notifications-outline" :size="20" class="mr-1" style="margin-top: -4px;" />
                            <span>Remind Customer</span>
                        </basicButton>

                    </Col>
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    import Slick from 'vue-slick';

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue';  

    /*  Modal Structure  */
    import mainModal from './main.vue';

    export default {
        props:{
            docUrl: {
                type: String,
                default: null
            },
        },
        components: { Slick, basicButton, Loader, mainModal  },
        data(){
            return{
                documents: [],
                hideModal: false,
                isLoadingDocs: false,
                //  Slick slider options
                slickOptions: {
                    dots: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                },
            }
        },
        methods: {
            getDocs() {
                
                if(this.docUrl){
                    
                    const self = this;

                    //  Start loader
                    self.isLoadingDocs = true;

                    console.log('Start getting docs...');

                    //  Additional data to eager load along with docs found
                    var connections = '';
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.docUrl + connections)
                            .then(({data}) => {
                                
                                console.log(data);

                                //  Stop loader
                                self.isLoadingDocs = false;

                                if(data){
                                    self.documents = data;
                                }
                                
                            })         
                            .catch(response => { 

                                //  Stop loader
                                self.isLoadingDocs = false;

                                console.log('viewProofOfPaymentModal.vue - Error getting docs...');
                                console.log(response);    
                            });
                }
            }
        },
        mounted () {
            this.getDocs();
            
        }
    }
</script>