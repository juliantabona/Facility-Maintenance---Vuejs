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

        <Row :gutter="20" v-if="company && !isLoading">
            <Col :span="24">

                <Row class="expand-row">
                    <Col span="24" v-if="company.logo_url">
                        <a :href="company.logo_url">
                            <img class="company-logo img-thumbnail mb-2 p-2 rounded rounded-circle" 
                                :src="company.logo_url" />
                        </a>
                    </Col>
                </Row>

                <Row class="expand-row">
                    <Col span="24">
                        <span class="d-inline-block cut-text"><b>Client: </b>{{ company.name ? company.name:'____' }}</span>
                        <span class="d-inline-block cut-text"><b>Address: </b>{{ company.address ? company.address:'____' }}</span>
                        <span class="d-inline-block cut-text"><b>City/Town: </b>{{ company.city ? company.city:'____' }}</span>
                    </Col>
                </Row>

                <Divider dashed class="mt-2 mb-2" />

                <Row class="expand-row">
                    <Col span="24">
                        <span class="d-inline-block cut-text"><b>Phone: </b>
                            {{ company.phone_ext ? '+'+company.phone_ext+'-':'___-' }}
                            {{ company.phone_num ? company.phone_num:'____' }}
                        </span>
                        
                        <span class="d-inline-block cut-text">
                            <b>Email: </b>{{ company.email ? company.email:'____' }}
                        </span>
                    </Col>
                </Row>

                <Divider dashed class="mt-2 mb-2" />

                <Row v-if="showContactsTagBtn" class="expand-row">
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

                <Row class="expand-row">
                    <Col span="24">
                        <Button v-if="showViewBtn" type="primary" class="float-right mb-1" size="small">
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

  export default {
    props: {
        company: {
            type: Object,
            default: () => {}
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
    data() {
      return {
           isLoading: false,
      }
    },
    methods: {
        
    } 
  };
  
</script>