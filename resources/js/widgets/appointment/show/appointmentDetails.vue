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

                                    <!-- Subject -->
                                    <h4 class="mb-3 mt-3 text-dark"><strong>Subject:</strong> {{ localAppointment.subject ? localAppointment.subject : '___' }}</h4>
                                    <!-- Agenda/Description -->
                                    <p class="text-dark"><strong>Agenda/Description:</strong> {{ localAppointment.agenda ? localAppointment.agenda : '___' }}</p>
                                    <!-- Deadline -->
                                    <p class="text-dark"><strong>Location:</strong> {{ localAppointment.location ? localAppointment.location : '___' }}</p>
                            
                                    <!-- Deadline -->
                                    <p class="text-dark"><strong>Session Duration:</strong> 
                                        
                                        <span v-if="getSessionHours()">{{ getSessionHours() == 1 ? getSessionHours() + ' hour ': getSessionHours() + ' hours ' }}</span>
                                        <span v-if="getSessionHours() && getSessionMinutes()">and</span>
                                        <span v-if="getSessionMinutes()">{{ getSessionMinutes() == 1 ? getSessionMinutes() + ' minute ': getSessionMinutes() + ' minutes ' }}</span>
                                        
                                    </p>
                            
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment Start Date -->
                                    <span class="text-dark">
                                        <strong>Start Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localAppointment.start_date | moment('DD MMM YYYY, H:mmA')">
                                            <span v-if="localAppointment.start_date">{{ localAppointment.start_date | moment('DD MMM YYYY, H:mmA') }}</span>
                                        </Poptip>
                                    </span>
                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment End Date -->
                                    <span class="text-dark">
                                        <strong>End Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localAppointment.end_date | moment('DD MMM YYYY, H:mmA')">
                                            <span v-if="localAppointment.end_date">{{ localAppointment.end_date | moment('DD MMM YYYY, H:mmA') }}</span>
                                        </Poptip>
                                    </span>
                                    
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment Category -->
                                    <span>
                                        <strong class="text-dark">Categories: </strong>
                                        <categoryTags :categories="localAppointment.categories"></categoryTags>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Assigned Staff -->
                                    <span>
                                        <strong class="text-dark">Assigned Staff: </strong>
                                        <assignedStaffTags :staffMembers="localAppointment.assigned_staff"></assignedStaffTags>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="24">
                                    <Divider dashed class="mt-3 mb-3" />
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment Created By -->
                                    <span>
                                        <strong class="text-dark">Created By: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="(localAppointment.createdBy || {}).position">
                                            <span><a href="#">{{ (localAppointment.createdBy || {}).full_name }}</a></span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment Created Date -->
                                    <span>
                                        <strong class="text-dark">Created Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localAppointment.created_at | moment('DD MMM YYYY, H:mmA')">
                                            <span v-if="localAppointment.created_at">{{ localAppointment.created_at | moment("DD MMM YYYY") }}</span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment Authorized By -->
                                    <span v-if="localAppointment.last_approved_activity">
                                        <strong class="text-dark">Authorized By: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="((localAppointment.last_approved_activity || {}).created_by || {}).position">
                                            <span><a href="#">{{ ((localAppointment.last_approved_activity || {}).created_by || {}).full_name }}</a></span>
                                        </Poptip>
                                    </span>
                                                                    
                                </Col>

                                <Col :span="12">

                                    <!-- Appointment Authorized Date -->
                                    <span v-if="localAppointment.last_approved_activity">
                                        <strong class="text-dark">Authorized Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="localAppointment.last_approved_activity.created_at | moment('DD MMM YYYY, H:mmA')">
                                            <span>{{ localAppointment.last_approved_activity.created_at | moment("DD MMM YYYY") }}</span>
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

                        <Col :span="24">
                            <!-- Subject -->
                            <el-form-item label="Subject:" prop="subject" class="mb-2">
                                <el-input v-model="localAppointment.subject" size="small" style="width:100%" placeholder="Enter appointment subject"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="24">
                            <!-- Agenda/Description -->
                            <el-form-item label="Agenda/Description:" prop="description" class="mb-2">
                                <el-input type="textarea" v-model="localAppointment.agenda" size="small" style="width:100%" placeholder="Enter appointment description"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="24">
                            <!-- Location -->
                            <el-form-item label="Location:" prop="location" class="mb-2">
                                <el-input v-model="localAppointment.location" size="small" style="width:100%" placeholder="Enter appointment location"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- Start Date -->
                            <el-form-item label="End At" prop="start_date" class="mb-2">
                                <el-date-picker v-model="localAppointment.start_date" type="date" placeholder="Start date" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- End Date -->
                            <el-form-item label="End At" prop="end_date" class="mb-2">
                                <el-date-picker v-model="localAppointment.end_date" type="date" placeholder="End date" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                        <Col :span="12">

                            <!-- Assigned Staff -->
                            <el-form-item label="Assigned Staff" prop="staff" class="mb-2">
                                <assignedStaffSelector
                                    :selectedStaff="localAppointment.assigned_staff"
                                    :tracker="localCreateMode ? 1 : 0"
                                    @updated:staff="$set(localAppointment, 'assigned_staff', $event)">
                                </assignedStaffSelector>
                            </el-form-item>

                        </Col>

                        <Col :span="12">

                          <!-- Categories -->
                            <el-form-item label="Appointment Type" prop="categories" class="mb-2">
                                <categorySelector
                                    modelType="appointment" 
                                    :selectedCategory="localAppointment.categories"
                                    :tracker="localCreateMode ? 1 : 0"
                                    @updated:category="$set(localAppointment, 'categories', $event)">
                                </categorySelector>
                            </el-form-item>

                        </Col>

                    </Row>
                    
                    <Row v-if="!hideSaveBtn">
                        <Col :span="24">
                            <hr class="mt-2" />
                            <!-- Save Changes Button -->
                            <Button class="float-right mt-2" type="success" size="large" @click="saveAppointment()">
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
    import categorySelector from './../../../components/_common/selectors/categorySelector.vue';  
    import assignedStaffSelector from './../../../components/_common/selectors/assignedStaffSelector.vue'; 

    /*  Tags   */
    import categoryTags from './../../../components/_common/tags/categoryTags.vue';  
    import assignedStaffTags from './../../../components/_common/tags/assignedStaffTags.vue'; 

    import moment from 'moment';
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            createMode: {
                type: Boolean,
                default: false
            },
            appointmentId: { 
                type: Number,
                default: null
            },
            appointment: { 
                type: Object,
                default: null
            },
            /*
             *  canSaveOnCreate checks if the parent has permitted for the appointment
             *  to be saved to the database. If canSaveOnCreate is set to true
             *  we will perform an ajax request to create the new appointment
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
            Loader, categoryTags, assignedStaffTags,
            categorySelector, assignedStaffSelector
        },
        data(){
            return {
                moment: moment,
                localAppointment: {},
                isLoading: false,
                ruleForm: { 
                    //  VALIDATION RULES

                },
                localEditMode: this.editMode,
                localCreateMode: this.createMode
            }
        },
        watch: {

            //  Watch for changes on the canSaveOnCreate value
            canSaveOnCreate: {
                handler: function (val, oldVal) {

                    //  Create a new appointment if canSaveOnCreate is set to true
                    if(this.appointmentId){
                        this.saveAppointment();
                    }else{
                        this.createNewAppointment();
                    }

                }
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;
                
                }
            },

            //  Watch for changes on the create mode value
            createMode: {
                handler: function (val, oldVal) {

                    //  Update the create mode value
                    this.localCreateMode = val;
                
                }
            }
        },
        computed:{
            formData(){
                return  {
                    subject: this.localAppointment.subject,  
                    agenda: this.localAppointment.agenda,  
                    start_date: this.localAppointment.start_date,   
                    end_date: this.localAppointment.end_date,  
                    location: this.localAppointment.location,   
                    categories: this.localAppointment.categories,   
                    assigned_staff: this.localAppointment.assigned_staff
                }
            }
        },
        methods: {
            getSessionHours(){
                var millisecondDiff = moment(this.localAppointment.end_date).format("x") - moment(this.localAppointment.start_date).format("x");

                if(millisecondDiff){
                    return moment.duration( millisecondDiff ).hours();
                }else{
                    return 0;
                }

            },
            getSessionMinutes(){
                var millisecondDiff = moment(this.localAppointment.end_date).format("x") - moment(this.localAppointment.start_date).format("x");

                if(millisecondDiff){
                    return moment.duration( millisecondDiff ).minutes();
                }else{
                    return 0;
                }
                
            },
            fetch(){
                
                if( this.appointmentId ){
                 
                    const self = this;

                    //  Additional data to eager load along with the appointment found
                    var connections = '?connections=client,categories,assignedStaff';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/appointments/'+this.appointmentId+connections)
                        .then(({data}) => {

                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            self.localAppointment = data;

                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
            saveAppointment() {

                var self = this;

                //  Start loader
                this.isSaving = true;

                console.log('Attempt to save appointment...');

                console.log( this.localAppointment );

                //  Form data to send
                let appointmentData = { 
                        appointment: {
                            subject: this.localAppointment.subject,
                            agenda: this.localAppointment.agenda,
                            start_date: this.localAppointment.start_date,
                            end_date: this.localAppointment.end_date,
                            location: this.localAppointment.location,
                            categories: this.localAppointment.categories.map( (category) => { return category.id } ),
                            assigned_staff: this.localAppointment.assigned_staff.map( (staff) => { return staff.id } ),
                            client_id: (this.localAppointment.client || {}).id,
                            client_model_type: (this.localAppointment.client || {}).model_type
                        }
                 };

                console.log(appointmentData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+this.localAppointment.id, appointmentData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Appointment saved sucessfully!');

                        self.$emit('updated:appointment', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/appointment/show/appointmentDetails.vue - Error saving appointment details...');
                        console.log(response);

                        //  Stop loader
                        self.isSaving = false;  
                    });
            },
            createNewAppointment() {
                const self = this;

                //  Start loader
                self.isCreating = true;

                console.log('Attempt to create new user...');

                //  Appointment data to send
                let appointmentData = {
                    appointment: this.formData
                };

                //  Additional data to eager load along with the appointment found
                var connections = '?connections=phones';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies'+connections, appointmentData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Created sucessfully!');

                        self.$emit('created:appointment', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/appointment/show/main.vue - Error creating appointment...');
                        console.log(response);

                        //  Stop loader
                        self.isSaving = false;     
    
                    });

            },
        },
        created(){
            if(this.appointment){
                this.localAppointment = this.appointment;
            }else{
                this.fetch();
            }
            
        }
    };
  
</script>