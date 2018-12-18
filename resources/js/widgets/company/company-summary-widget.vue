<style scoped>
    .company-logo{
        width: 80px !important;
        height: 80px;
    }

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important; 
        width: 235px !important; 
        white-space: nowrap !important;
    }
</style>

<template>

    <Card class="company-summary-widget" :style="{ width: '100%' }">
        
        <div slot="title">
            <h5 v-if="type == 'client'"><Icon type="ios-happy-outline" :size="18" class="mr-2"></Icon> Client Details</h5>
            <h5 v-if="type == 'contractor'"><Icon type="ios-briefcase-outline" :size="18" class="mr-2"></Icon> Contractor Details</h5>
        </div>
        <div slot="extra" v-if="showMenuBtn">
            <Dropdown trigger="click">
                <a href="javascript:void(0)">
                    <Icon type="md-more" :size="16"></Icon>
                </a>
                <DropdownMenu slot="list">
                    <DropdownItem v-if="showEditBtn">Edit</DropdownItem>
                    <DropdownItem v-if="showRemoveBtn">Remove</DropdownItem>
                    <DropdownItem v-if="showAddContactBtn">Add Contact</DropdownItem>
                    <DropdownItem v-if="showDownloadProfileBtn">Download Profile</DropdownItem>
                    <DropdownItem v-if="showDownloadLogoBtn">Download Logo</DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </div>

        <Loader v-if="isLoading" :loading="isLoading"></Loader>

        <Row :gutter="20" v-if="localCompany">
            <Col :span="24">

                <Row>
                    <Col span="24" v-if="localCompany.logo_url">
                        <a :href="localCompany.logo_url">
                            <img class="company-logo img-thumbnail mb-2 p-2 rounded rounded-circle" 
                                :src="localCompany.logo_url" />
                        </a>
                    </Col>
                </Row>

                <Row>
                    <Col span="24">
                        <span class="d-inline-block cut-text"><b>Client: </b>{{ localCompany.name ? localCompany.name:'____' }}</span>
                        <span class="d-inline-block cut-text"><b>Address: </b>{{ localCompany.address ? localCompany.address:'____' }}</span>
                        <span class="d-inline-block cut-text"><b>City/Town: </b>{{ localCompany.city ? localCompany.city:'____' }}</span>
                    </Col>
                </Row>

                <Divider dashed class="mt-2 mb-2" />

                <Row>
                    <Col span="24">
                        <span class="d-inline-block cut-text"><b>Phone: </b>
                            {{ localCompany.phone_ext ? '+'+localCompany.phone_ext+'-':'___-' }}
                            {{ localCompany.phone_num ? localCompany.phone_num:'____' }}
                        </span>
                        
                        <span class="d-inline-block cut-text">
                            <b>Email: </b>{{ localCompany.email ? localCompany.email:'____' }}
                        </span>
                    </Col>
                </Row>

                <Divider dashed class="mt-2 mb-2" />

                <Row v-if="showContactsTagBtn">
                    <Col span="24">
                        <span class="d-block float-right">
                            <Poptip word-wrap width="200" trigger="hover" content="Click to view Contacts">
                                <Tag type="border" color="green">+2 Contacts</Tag>
                            </Poptip>
                            <Button icon="ios-add" type="dashed" size="small">Add</Button>
                        </span>
                    </Col>
                </Row>

                <Divider dashed class="mt-2 mb-2" />

                <Row>
                    <Col span="24">
                        <Button v-if="showViewBtn" type="primary" class="float-right mb-1" size="small" @click="viewCompany">
                            <span>View</span>
                            <Icon type="md-arrow-forward" />
                        </Button>
                    </Col>
                </Row>

            </Col>
        </Row>

    </Card>

</template>

<script>

    import Loader from './../../components/Loader.vue';

    export default {
        components: { Loader },
        props: {
            /*  You can either provide a company object,
             *  Company Id, or Company Branch Id to build 
             *  the widget 
             */
            company: {
                type: Object,
                default: () => {}
            },
            companyId: {
                type: Number,
                default: null
            },
            companyBranchId: {
                type: Number,
                default: null
            },
            type: {
                type: String, 
                default: '',
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            showViewBtn: {
                type: Boolean,
                default: true
            },
            showEditBtn: {
                type: Boolean,
                default: true
            },
            showRemoveBtn: {
                type: Boolean,
                default: true
            },
            showAddContactBtn: {
                type: Boolean,
                default: true
            },
            showDownloadProfileBtn: {
                type: Boolean,
                default: true
            },
            showDownloadLogoBtn: {
                type: Boolean,
                default: true
            },
            showContactsTagBtn: {
                type: Boolean,
                default: true
            }

        },
        data(){
            return {
                isLoading: false,
                localCompany: this.company,
                modelId: null,
                modelType: ''
            }
        },
        methods: {
            fetch(){

                if(this.modelId != null && this.modelType != ''){
                 
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.modelId+'?model='+this.modelType)
                        .then(({data}) => {

                            //  Stop loader
                            self.isLoading = false;

                            self.localCompany = data;
                
                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
            getCompany: function(){
                if(!this.company){

                    /*  Try fetching the company using the company id
                    *  Otherwise try using the company branch id
                    */

                    if(this.companyId != null){

                        this.modelId = this.companyId;
                        this.modelType = 'Company';

                    }else if(this.companyBranchId != null){

                        this.modelId = this.companyBranchId;
                        this.modelType = 'CompanyBranch';

                    }

                    this.fetch();

                }
            },
            viewCompany: function(){
                this.$router.push({ name: 'show-client', params: { id: this.localCompany.id } });
            }
        },
        watch: {
            'company'() {
                this.getCompany();
            },
            'companyId'() {
                this.getCompany();
            },
            'companyBranchId'() {
                this.getCompany();
            }
        },
        created(){
            //  If we did not provide the company object
            this.getCompany();
        }
    };
    
</script>