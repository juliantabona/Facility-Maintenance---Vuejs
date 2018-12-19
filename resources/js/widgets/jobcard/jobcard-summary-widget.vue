<template>

    <Card class="jobcard-summary-widget" :style="{ width: '100%' }">

        <div v-if ="showHeaderSection" slot="title">
            <h5><Icon type="ios-copy-outline" :size="18" class="mr-2"></Icon> Jobcard Summary</h5>
        </div>
    
        <div v-if ="showHeaderSection" slot="extra">
            <span v-if="showAuthourizedStatus && jobcard.authourizedBy"><strong>Authourized: </strong>
                <span class="mr-2">
                    <Poptip word-wrap width="200" trigger="hover" content="Jobcard is authourized for processing">
                        <span><Icon type="md-checkmark-circle-outline" :size="20" color="#19be6b"/></span>
                    </Poptip>
                </span>
            </span>
            <span v-if="showProcessStatus && jobcard.statusSummary"><strong>Status: </strong>
                <statusSummaryTag :statusSummary="jobcard.statusSummary"></statusSummaryTag>
            </span>
            <Dropdown trigger="click" v-if="showMenuBtn">
                <a href="javascript:void(0)">
                    <Icon type="md-more" :size="16"></Icon>
                </a>
                <DropdownMenu slot="list">
                    <DropdownItem v-if="showMenuEditBtn">Edit</DropdownItem>
                    <DropdownItem v-if="showMenuTrashBtn">Trash</DropdownItem>
                    <DropdownItem v-if="showMenuAddClientBtn">Add Client</DropdownItem>
                    <DropdownItem v-if="showMenuAddSupplierBtn">Add Supplier</DropdownItem>
                    <DropdownItem v-if="showMenuAddLabourBtn">Add Labour</DropdownItem>
                    <DropdownItem v-if="showMenuAddAssetBtn">Add Asset</DropdownItem>
                </DropdownMenu>
            </Dropdown>
    
        </div>

        <Row v-if="showDescriptionSection">
            <Col v-if="showTitle" span="24">
                <p class="mb-2"><strong>Title:</strong> {{ jobcard.title }}</p>
            </Col>
            <Col v-if="showDescription" span="24">
                <p>
                    <span><strong>Description: </strong></span>
                    <span>{{ jobcard.description }}</span>
                </p>
            </Col>
            <Col v-if="showDeadline" span="24">
                <p class="mb-2">
                    <span class="d-block mt-2"><strong>Deadline: </strong>{{ jobcard.deadlineInWords }}</span>
                </p>
            </Col>
            <Col v-if="showStartDate" span="12">
                <span>
                    <Poptip word-wrap width="200" trigger="hover" :content="jobcard.start_date | moment('DD MMM YYYY, H:mmA')">
                        <span><strong>Start Date: </strong></span>
                        <span v-if="jobcard.start_date">{{ jobcard.start_date | moment("DD MMM YYYY") }}</span>
                    </Poptip>
                </span>
            </Col>
            <Col v-if="showEndDate" span="12">
                <span>
                    <Poptip word-wrap width="200" trigger="hover" :content="jobcard.end_date | moment('DD MMM YYYY, H:mmA')">
                        <span><strong>End Date: </strong></span>
                        <span v-if="jobcard.end_date">{{ jobcard.end_date | moment("DD MMM YYYY") }}</span>
                    </Poptip>
                </span>
            </Col>

            <Col span="24">
                <Divider dashed class="mt-3 mb-3" />
            </Col>

        </Row>

        <Row v-if="showStatusSection">
            <Col v-if="showPriority" span="12">
                <span><strong>Priority: </strong></span>
                <priorityTag :priorities="jobcard.priorities" :showEditBtn="true"></priorityTag>
            </Col>
            <Col v-if="showCategory" span="12">
                <span><strong>Category: </strong></span>
                <categoryTag :categories="jobcard.categories" :showEditBtn="true"></categoryTag>
            </Col>
            <Col v-if="showCostCenters" span="24" class="mt-2">
                <span><strong>Cost Centers: </strong></span>
                <costcenterTag :costcenters="jobcard.costcenters" :showAddBtn="true"></costcenterTag>
            </Col>

            <Col span="24">
                <Divider dashed class="mt-3 mb-3" />
            </Col>

        </Row>

        <Row v-if ="showPublishSection">
            <Col v-if="jobcard.createdBy && showCreatedBy" span="12">
                <span><strong>Created By: </strong></span>
                <Poptip word-wrap width="200" trigger="hover" :content="getPostion(jobcard.createdBy)">
                    <span><a href="#">{{ getFullName(jobcard.createdBy) }}</a></span>
                </Poptip>
            </Col>
            <Col v-if="jobcard.createdBy && showCreatedByDate" span="12">
                <span>
                    <Poptip word-wrap width="200" trigger="hover" :content="jobcard.created_at | moment('DD MMM YYYY, H:mmA')">
                        <span><strong>Created Date: </strong></span>
                        <span v-if="jobcard.created_at">{{ jobcard.created_at | moment("DD MMM YYYY") }}</span>
                    </Poptip>
                </span>
            </Col>
            <Col v-if="jobcard.authourizedBy && showAuthourizedBy" span="12">
                <span><strong>Authorized By:</strong></span>
                <Poptip word-wrap width="200" trigger="hover" :content="getPostion(jobcard.authourizedBy)">
                    <span><a href="#">{{ getFullName(jobcard.authourizedBy) }}</a></span>
                </Poptip>
            </Col>
            <Col v-if="jobcard.authourizedBy && showAuthourizedByDate" span="12">
                <span>
                    <Poptip word-wrap width="200" trigger="hover" :content="getAuthourizedDate() | moment('DD MMM YYYY, H:mmA')">
                        <span><strong>Authorized Date:</strong></span>
                         <span v-if="getAuthourizedDate()">{{ getAuthourizedDate() | moment("DD MMM YYYY") }}</span>
                        {{ getAuthourizedDate() | moment("DD MMM YYYY") }}
                    </Poptip>
                </span>
            </Col>
            
            <Col span="24">
                <Divider v-if ="jobcard.createdBy || jobcard.authourizedBy" dashed class="mt-3 mb-3" />
            </Col>

        </Row>

        <Row v-if ="showResourceSection" class="expand-row mb-1">
            <Col span="24">
                <span class="d-block float-right">
                    <Poptip word-wrap width="200" trigger="hover" content="Click to view Suppliers">
                        <Tag type="border" color="green">+2 Suppliers</Tag>
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

            <Col span="24">
                <Divider dashed class="mt-2 mb-4" />
            </Col>

        </Row>

        <Row v-if ="showActionToolbalSection">
            <Col span="24">
                <Button v-if ="showViewBtn" type="primary" class="float-right" @click.native="viewJobcard">
                    View
                    <Icon type="md-arrow-forward" />
                </Button>
                <downloadJobcardBtn v-if ="showDownloadBtn" :jobcardId="jobcard.id" class="float-right mr-1"></downloadJobcardBtn>
                <Button v-if ="showSendBtn" type="default" class="float-right mr-1">
                    <Icon type="ios-send-outline" :size="18" :style="{ marginTop:'-3px' }"/>
                    Send
                </Button>
                <div v-if ="showPublicBtn" class="float-left mt-2 mr-4">
                    <Poptip word-wrap width="200" trigger="hover" 
                            content="Advertise jobcard to the general public by pushing to websites and social media. This will allow third-party suppliers to see and respond to your jobcard. You will be alerted on the platform and via email.">
                        <span>
                            <Icon type="md-globe mr-1" :size="18" />
                            <strong>Make Public:</strong>
                            <i-switch :value="jobcard.is_public ? true: false" class="ml-1" size="large">
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

    import priorityTag from './../../components/priority/priority-tag.vue';
    import categoryTag from './../../components/category/category-tag.vue';
    import costcenterTag from './../../components/costcenter/costcenter-tag.vue';
    import statusSummaryTag from './../../components/jobcard/status-summary-tag.vue';
    import downloadJobcardBtn from './../../components/jobcard/download-button.vue';    

    export default {
        components: { priorityTag, categoryTag, costcenterTag, statusSummaryTag, downloadJobcardBtn },
        props: {
            jobcard: {
                type: Object,
                default: () => {}
            },
            /*  Menu related Options  */
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
            showMenuAddSupplierBtn: {
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
            /*  Section Options  */
            showHeaderSection: {
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
            },
            /*  Single Options    */

            /*  Header Options    */
            showAuthourizedStatus: {
                type: Boolean,
                default: true
            },  
            showProcessStatus: {
                type: Boolean,
                default: true
            }, 
            /*  Body Options    */
            showTitle: {
                type: Boolean,
                default: true
            }, 
            showDescription: {
                type: Boolean,
                default: true
            }, 
            showDeadline: {
                type: Boolean,
                default: true
            }, 
            showStartDate: {
                type: Boolean,
                default: true
            }, 
            showEndDate: {
                type: Boolean,
                default: true
            }, 
            showPriority: {
                type: Boolean,
                default: true
            }, 
            showCategory: {
                type: Boolean,
                default: true
            }, 
            showCostCenters: {
                type: Boolean,
                default: true
            },
            showCreatedBy: {
                type: Boolean,
                default: true
            }, 
            showCreatedByDate: {
                type: Boolean,
                default: true
            }, 
            showAuthourizedBy: {
                type: Boolean,
                default: true
            }, 
            showAuthourizedByDate: {
                type: Boolean,
                default: true
            },
            /*  Footer Options    */
            showViewBtn: {
                type: Boolean,
                default: true
            }, 
            showDownloadBtn: {
                type: Boolean,
                default: true
            }, 
            showSendBtn: {
                type: Boolean,
                default: true
            }, 
            showPublicBtn: {
                type: Boolean,
                default: true
            },
        },
        data() {
            return {
                isLoading: false,
                /*  We need to pull moment.js to prevent the "Property or method "moment" is not defined on the instance but referenced during render."
                 *  This error only occurs when we use moment in a conditional statement. Refer to the jobcard start_date and end_date for example.
                 *  To avoid this we must pull moment using our global Vue instance.
                 */
                moment: Vue.moment,
            }
        },
        computed:{
            statusSummary: function(){
                return (this.jobcard.statusSummary || {});
            }
        },
        methods: {
            getFullName: function(user){
                let first_name = (user || {}).first_name|| '';
                let last_name = (user || {}).last_name|| '';

                return first_name+' '+last_name;
            },
            getPostion(user){
                return (user || {}).position || '';
            },
            getAuthourizedDate(){
                
                var x, recentActivity = this.jobcard.recent_activities || {};

                for(x=0; x < _.size(recentActivity); x++){
                    if(recentActivity[x].activity.type == 'authourized'){
                        return recentActivity[x].created_at; 
                    }
                }
            },
            viewJobcard: function(){
                this.$router.push({ name: 'show-jobcard', params: { id: this.jobcard.id } });
            }
        } 
    };
  
</script>