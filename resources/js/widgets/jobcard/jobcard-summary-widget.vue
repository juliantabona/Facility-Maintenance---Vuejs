<style scoped>

</style>

<template>

    <Card class="company-summary-widget" :style="{ width: '100%' }">

        <div slot="title">
            <h5><Icon type="ios-copy-outline" :size="18" class="mr-2"></Icon> Jobcard Summary</h5>
        </div>

        <div slot="extra">
            <span><strong>Status: </strong></span>
            <span class="mr-2">
                <Poptip word-wrap width="200" trigger="hover" title="Current Status" content="In this state the job is being inspected to ensure good work as per given specifications.">
                    <Tag color="success">Closed</Tag>
                </Poptip>
            </span>
            <Dropdown trigger="click" v-if="showMenuBtn">
                <a href="javascript:void(0)">
                    <Icon type="md-more" :size="16"></Icon>
                </a>
                <DropdownMenu slot="list">
                    <DropdownItem v-if="showMenuEditBtn">Edit</DropdownItem>
                    <DropdownItem v-if="showMenuTrashBtn">Trash</DropdownItem>
                    <DropdownItem v-if="showMenuAddClientBtn">Add Client</DropdownItem>
                    <DropdownItem v-if="showMenuAddContractorBtn">Add Contractor</DropdownItem>
                    <DropdownItem v-if="showMenuAddLabourBtn">Add Labour</DropdownItem>
                    <DropdownItem v-if="showMenuAddAssetBtn">Add Asset</DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </div>

        <Row v-if="showDescriptionSection">
            <Col span="24">

                <p class="mb-2"><strong>Title:</strong> Repair Of Air Conditioners</p>
                <p>
                    <span><strong>Description: </strong></span>
                    <span>Repair of six (6) air conditioners. Two have broken fan blades, while the remaining have damaged power cables.</span>
                </p>
                <p class="mb-2"><span class="d-block mt-2"><strong>Deadline: </strong>Deadline passed 1 day ago</span></p>
            </Col>
        </Row>

        <Divider v-if="showDescriptionSection" dashed class="mt-2 mb-3" />

        <Row v-if="showStatusSection">
            <Col span="12">
                <span><strong>Category: </strong></span>
                <span>
                    <Poptip word-wrap width="200" trigger="hover" content="Any heating or cooling device or utility">
                        <Tag type="border" color="primary">Air-Conditioning</Tag>
                    </Poptip>
                    <Button type="dashed" size="small"><Icon type="ios-create-outline" :size="18" class="mr-2"></Icon></Button>
                </span>
            </Col>
            <Col span="12">
                <span><strong>Cost Centers: </strong></span>
                <span>
                    <Poptip word-wrap width="200" trigger="hover" content="Costs by maintenance work">
                        <Tag type="border" color="primary">Maintenance</Tag>
                    </Poptip>
                    <Button icon="ios-add" type="dashed" size="small">Add</Button>
                </span>
            </Col>
        </Row>

        <Divider v-if ="showStatusSection" dashed class="mt-3 mb-3" />

        <Row v-if ="showPublishSection">
            <Col span="12">
                <span><strong>Created By: </strong></span>
                <Poptip word-wrap width="200" trigger="hover" content="Staff Member">
                    <span><a href="#">Julian Tabona</a></span>
                </Poptip>
            </Col>
            <Col span="12">
                <span>
                    <Poptip word-wrap width="200" trigger="hover" content="23 May 2018 - 08:15AM">
                        <span><strong>Created Date: </strong></span>
                        23 May 2018
                    </Poptip>
                </span>
            </Col>
            <Col span="12">
                <span><strong>Authorized By:</strong></span>
                <Poptip word-wrap width="200" trigger="hover" content="Project Manager">
                    <span><a href="#">Kgosi Tabona</a></span>
                </Poptip>
            </Col>
            <Col span="12">
                <span>
                    <Poptip word-wrap width="200" trigger="hover" content="24 May 2018 - 09:25AM">
                        <span><strong>Authorized Date:</strong></span>
                        24 May 2018
                    </Poptip>
                </span>
            </Col>
        </Row>

        <Divider v-if ="showPublishSection" dashed class="mt-2 mb-2" />

        <Row v-if ="showResourceSection" class="expand-row mb-1">
            <Col span="24">
                <span class="d-block float-right">
                    <Poptip word-wrap width="200" trigger="hover" content="Click to view Contractors">
                        <Tag type="border" color="green">+2 Contractors</Tag>
                    </Poptip>
                    <Poptip word-wrap width="200" trigger="hover" content="Click to view Assets">
                        <Tag type="border" color="green">+3 Assets</Tag>
                    </Poptip>
                    <Poptip word-wrap width="200" trigger="hover" content="Click to view Labour">
                        <Tag type="border" color="green">+1 Labour</Tag>
                    </Poptip>
                    <Poptip word-wrap width="200" trigger="hover" content="Click to view Quotation">
                        <Tag type="border" color="green">+1 Quotation</Tag>
                    </Poptip>
                    -
                    <Poptip word-wrap width="200" trigger="hover" content="Click to view Attachments">
                        <Tag type="border" color="default">+2 Attachments</Tag>
                    </Poptip>
                </span>
            </Col>
        </Row>

        <Divider v-if ="showResourceSection" dashed class="mt-2 mb-4" />

        <Row v-if ="showActionToolbalSection">
            <Col span="24">
                <Button type="primary" class="float-right" @click.native="viewJobcard">
                    View
                    <Icon type="md-arrow-forward" />
                </Button>
                <Button type="default" class="float-right mr-1">
                    <Icon type="ios-download-outline" :size="18" :style="{ marginTop:'-3px' }"/>
                    Download
                </Button>
                <Button type="default" class="float-right mr-1">
                    <Icon type="ios-send-outline" :size="18" :style="{ marginTop:'-3px' }"/>
                    Send
                </Button>
                <div class="float-left mt-2 mr-4">
                    <Poptip word-wrap width="200" trigger="hover" 
                            content="Advertise jobcard to the general public by pushing to websites and social media. This will allow third-party contractors to see and respond to your jobcard. You will be alerted on the platform and via email.">
                        <span>
                            <Icon type="md-globe mr-1" :size="18" />
                            <strong>Make Public:</strong>
                            <i-switch class="ml-1" size="large">
                                <span slot="open">Yes</span>
                                <span slot="close">No</span>

                            </i-switch>
                        </span>
                    </Poptip>
                </div>
            </Col>
        </Row>

    </Card>

</template>

<script>

  export default {
    props: {
        jobcard: {
            type: Object,
            default: () => {}
        },
        showMenuBtn: {
            type: Boolean,
            default: true
        },
        showMenuEditBtn: {
            type: Boolean,
            default: true
        },
        showMenuTrashBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddClientBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddContractorBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddLabourBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddAssetBtn: {
            type: Boolean,
            default: true
        },
        showDescriptionSection: {
            type: Boolean,
            default: true
        },
        showStatusSection: {
            type: Boolean,
            default: true
        },
        showPublishSection: {
            type: Boolean,
            default: true
        },
        showResourceSection: {
            type: Boolean,
            default: true
        },
        showActionToolbalSection: {
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
        viewJobcard: function(){
            this.$router.push({ name: 'show-jobcard', params: { id: this.jobcard.id } });
        }
    } 
  };
  
</script>