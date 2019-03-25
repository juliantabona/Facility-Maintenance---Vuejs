<style scoped>

    .el-form-item >>> .el-form-item__label{
        margin:0 !important;
        padding:0 !important;
        line-height: 24px !important;
    }

    .form-label{
        font-size:14px;
    }

</style>

<template>

        <Row>

            <Col :span="24">

                <!-- Loader -->
                <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>
                
                <div v-if="!isLoading && !localEditMode">

                    <Row :gutter="20" class="mb-1" :style="{ lineHeight: '1.8em' }">

                        <Col :span="24">
                            <Row class="mb-3">
                                <Col :span="24">

                                    <!-- Title -->
                                    <h4 class="mb-3 mt-3 text-dark"><strong>Title:</strong> {{ localJobcard.title ? localJobcard.title : '___' }}</h4>
                                    <!-- Description -->
                                    <p class="text-dark"><strong>Description:</strong> {{ localJobcard.description ? localJobcard.description : '___' }}</p>
                                    <!-- Description -->
                                    <p class="text-dark"><strong>Deadline:</strong> {{ localJobcard.deadlineInWords ? localJobcard.deadlineInWords : '___' }}</p>
                            
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Start Date -->
                                    <span class="text-dark">
                                        <strong>Start Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localJobcard.start_date | moment('DD MMM YYYY, H:mmA')">
                                            <span v-if="localJobcard.start_date">{{ localJobcard.start_date | moment("DD MMM YYYY") }}</span>
                                        </Poptip>
                                    </span>
                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard End Date -->
                                    <span class="text-dark">
                                        <strong>End Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localJobcard.end_date | moment('DD MMM YYYY, H:mmA')">
                                            <span v-if="localJobcard.end_date">{{ localJobcard.end_date | moment("DD MMM YYYY") }}</span>
                                        </Poptip>
                                    </span>
                                    
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Priority -->
                                    <span>
                                        <strong class="text-dark">Priority: </strong>
                                        <priorityTag :priority="localJobcard.priority"></priorityTag>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Category -->
                                    <span>
                                        <strong class="text-dark">Categories: </strong>
                                        <categoryTags :categories="localJobcard.categories"></categoryTags>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Cost Center -->
                                    <span>
                                        <strong class="text-dark">Cost Centers: </strong>
                                        <costcenterTags :costcenters="localJobcard.costcenters"></costcenterTags>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                                <Col :span="24">

                                    <!-- Assigned Staff -->
                                    <span>
                                        <strong class="text-dark">Assigned Staff: </strong>
                                        <assignedStaffTags :staffMembers="localJobcard.assigned_staff"></assignedStaffTags>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Created By -->
                                    <span>
                                        <strong class="text-dark">Created By: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="(localJobcard.createdBy || {}).position">
                                            <span><a href="#">{{ (localJobcard.createdBy || {}).full_name }}</a></span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Created Date -->
                                    <span>
                                        <strong class="text-dark">Created Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localJobcard.created_at | moment('DD MMM YYYY, H:mmA')">
                                            <span v-if="localJobcard.created_at">{{ localJobcard.created_at | moment("DD MMM YYYY") }}</span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Authorized By -->
                                    <span v-if="localJobcard.last_approved_activity">
                                        <strong class="text-dark">Authorized By: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="((localJobcard.last_approved_activity || {}).created_by || {}).position">
                                            <span><a href="#">{{ ((localJobcard.last_approved_activity || {}).created_by || {}).full_name }}</a></span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Jobcard Authorized Date -->
                                    <span v-if="localJobcard.last_approved_activity">
                                        <strong class="text-dark">Authorized Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localJobcard.last_approved_activity.created_at | moment('DD MMM YYYY, H:mmA')">
                                            <span>{{ localJobcard.last_approved_activity.created_at | moment("DD MMM YYYY") }}</span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                            </Row>
                        </Col>

                    </Row>

                </div>

                <el-form v-if="!isLoading && localEditMode" 
                         label-position="top" label-width="100px" 
                         :model="formData">
                    
                    <Row :gutter="20" class="mb-1">

                        <Col v-if="localEditMode" :span="24" class="mt-1 mb-2">
                            <Alert>This jobcard is now editable.</Alert>
                        </Col>

                        <Col :span="24">
                            <!-- Title -->
                            <el-form-item label="Title:" prop="title" class="mb-2">
                                <el-input v-model="localJobcard.title" size="small" style="width:100%" placeholder="Enter jobcard title"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="24">
                            <!-- Description -->
                            <el-form-item label="Description:" prop="description" class="mb-2">
                                <el-input type="textarea" v-model="localJobcard.description" size="small" style="width:100%" placeholder="Enter jobcard description"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- Start Date -->
                            <el-form-item label="Start Date" prop="start_date" class="mb-2">
                                <el-date-picker v-model="localJobcard.start_date" type="date" placeholder="Job start date" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- End Date -->
                            <el-form-item label="End Date" prop="end_date" class="mb-2">
                                <el-date-picker v-model="localJobcard.end_date" type="date" placeholder="Job end date" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- Priority -->
                            <el-form-item label="Priority" prop="priority" class="mb-2">
                                <prioritySelector
                                    modelType="jobcard" 
                                    :selectedPriority="localJobcard.priority"
                                    @updated:priority="localJobcard.priority = $event">
                                </prioritySelector>
                            </el-form-item>

                            <!-- Cost Centers -->
                            <el-form-item label="Cost Centers" prop="costcenters" class="mb-2">
                                <costcenterSelector
                                    modelType="jobcard" 
                                    :selectedCostCenter="localJobcard.costcenters"
                                    @updated:costcenter="localJobcard.costcenters = $event">
                                </costcenterSelector>
                            </el-form-item>

                            <!-- Assigned Staff -->
                            <el-form-item label="Assigned Staff" prop="staff" class="mb-2">
                                <assignedStaffSelector
                                    :selectedStaff="localJobcard.assigned_staff"
                                    @updated:staff="localJobcard.assigned_staff = $event">
                                </assignedStaffSelector>
                            </el-form-item>

                        </Col>

                        <Col :span="12">
                            <!-- Categories -->
                            <el-form-item label="Categories" prop="categories" class="mb-2">
                                <categorySelector
                                    modelType="jobcard" 
                                    :selectedCategory="localJobcard.categories"
                                    @updated:category="localJobcard.categories = $event">
                                </categorySelector>
                            </el-form-item>
                        </Col>

                    </Row>
                    
                    <Row v-if="!hideSaveBtn">
                        <Col :span="24">
                            <hr class="mt-2" />
                            <!-- Save Changes Button -->
                            <Button class="float-right mt-2" type="success" size="large" @click="saveJobcard()">
                                <span>Save Changes</span>
                            </Button>
                        </Col>
                    </Row>

                </el-form>

            </Col>
            
        </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    /*  Selectors   */
    import prioritySelector from './../../../components/_common/selectors/prioritySelector.vue'; 
    import categorySelector from './../../../components/_common/selectors/categorySelector.vue'; 
    import costcenterSelector from './../../../components/_common/selectors/costcenterSelector.vue'; 
    import assignedStaffSelector from './../../../components/_common/selectors/assignedStaffSelector.vue'; 

    /*  Tags   */
    import priorityTag from './../../../components/_common/tags/priorityTag.vue'; 
    import categoryTags from './../../../components/_common/tags/categoryTags.vue'; 
    import costcenterTags from './../../../components/_common/tags/costcenterTags.vue'; 
    import assignedStaffTags from './../../../components/_common/tags/assignedStaffTags.vue'; 
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            jobcardId: { 
                type: Number,
                default: null
            },
            jobcard: { 
                type: Object,
                default: null
            },
            /*
             *  canSaveOnCreate checks if the parent has permitted for the jobcard
             *  to be saved to the database. If canSaveOnCreate is set to true
             *  we will perform an ajax request to create the new jobcard
             *  using our formData information.
             */
            canSaveOnCreate:{
                type: Boolean,
                default: false          
            },
            hideSaveBtn: { 
                type: Boolean,
                default: false
            }
        },
        components: { 
            Loader, priorityTag, categoryTags, costcenterTags, assignedStaffTags,
            prioritySelector, categorySelector, costcenterSelector,
            assignedStaffSelector
        },
        data(){
            return {
                localJobcard: {},
                isLoading: false,
                ruleForm: { 
                    //  VALIDATION RULES

                },
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the canSaveOnCreate value
            canSaveOnCreate: {
                handler: function (val, oldVal) {

                    //  Create a new jobcard if canSaveOnCreate is set to true
                    if(this.jobcardId){
                        this.saveJobcard();
                    }else{
                        this.createNewJobcard();
                    }

                }
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;
                
                }
            }
        },
        computed:{
            formData(){
                return  {
                    title: this.localJobcard.title,  
                    description: this.localJobcard.description,  
                    startDate: this.localJobcard.startDate,  
                    endDate: this.localJobcard.endDate,  
                    priority: this.localJobcard.priority,  
                    categories: this.localJobcard.categories,  
                    costcenters: this.localJobcard.costcenters,  
                    assigned_staff: this.localJobcard.assigned_staff
                }
            }
        },
        methods: {
            fetch(){
                
                if( this.jobcardId ){
                 
                    const self = this;

                    //  Additional data to eager load along with the jobcard found
                    var connections = '?connections=priority,categories,costcenters,assignedStaff';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/jobcards/'+this.jobcardId+connections)
                        .then(({data}) => {

                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            self.localJobcard = data;

                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
            saveJobcard() {
                const self = this;

                //  Start loader
                self.isSaving = true;

                console.log('Attempt to save jobcard details...');

                //  Jobcard data to send
                let jobcardData = {
                    jobcard: this.formData
                };

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=priority,categories,costcenters,assignedStaff';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies/'+this.localJobcard.id + connections, jobcardData)
                    .then(({data}) => {

                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Jobcard saved sucessfully!');

                        self.$emit('updated:jobcard', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/jobcard/show/main.vue - Error saving jobcard details...');
                        console.log(response);

                        //  Stop loader
                        self.isLoggingIn = false;     
    
                    });

            },
            createNewJobcard() {
                const self = this;

                //  Start loader
                self.isCreating = true;

                console.log('Attempt to create new user...');

                //  Jobcard data to send
                let jobcardData = {
                    jobcard: this.formData
                };

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=phones';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies'+connections, jobcardData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Created sucessfully!');

                        self.$emit('created:jobcard', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/jobcard/show/main.vue - Error creating jobcard...');
                        console.log(response);

                        //  Stop loader
                        self.isLoggingIn = false;     
    
                    });

            },
        },
        created(){
            if(this.jobcard){
                this.localJobcard = this.jobcard;
            }else{
                this.fetch();
            }
            
        }
    };
  
</script>