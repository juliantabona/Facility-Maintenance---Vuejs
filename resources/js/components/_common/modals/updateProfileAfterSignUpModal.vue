<style scoped>

    .el-form-item__error {
        line-height: 0.8;
        padding-top: 0px;
    }

</style>
 <template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :isSaving="false" 
            :hideModal="hideModal"
            :showCloseBtn="false"
            :modalClosable="false"
            title=""
            okText="" cancelText=""
            @on-ok="" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Activity Number Card -->
                <Row :gutter="20">
                    
                    <Col v-if="currentStage == 1" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">Welcome To OQ Suite</h1>
                            <p class="text-center">Hi Julian, Welcome to your Mobile Office. We would personally like to give you a guided tour of our platform.</p> 
                            <p class="text-center mb-3">It's really easy to connect and stay engaged with your customers using OQ Suite. </p>
                            <p class="font-weight-bold text-center">- Lets get started with the basics -</p>  
                        </div>

                        <!-- Focus Ripple  -->
                        <focusRipple color="blue" :ripple="true" class="d-block">

                            <!-- Create Company Button  -->
                            <Button type="primary" size="large" @click="nextStep()"
                                    class="button_1 d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Setup Your Mobile Office</span>
                                <Icon type="ios-arrow-round-forward" />
                            </Button>

                        </focusRipple>

                        <span class="d-block mt-4 mb-4 text-center">Takes less than a minute</span>
                        
                    </Col>

                    <Col v-if="currentStage == 2" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">What Would You Like To Manage?</h1>
                            <p class="text-center">OQ Suite offers a range of tools to help you run your business smoothly. Tick the tools you would like to use.</p> 
                            <p class="font-weight-bold text-center mb-4">- You can always change this later -</p>  
                        </div>

                        <Row :gutter="12" class="mb-5">
                            <Col v-for="(tool, i) in availableTools" :key="i" :span="8">
                                <IconAndCounterCard :title="tool.name" :icon="tool.icon" 
                                                    class="mb-2" type="success"
                                                    :showCheckMark="true"
                                                    :checkMarkVisibility="selectedTools.includes(tool.name)"
                                                    @click.native="toggleOption(tool.name, i)"
                                                    v-on:mouseover="updateHoveredTool(tool)"
                                                    v-on:mouseout="updateHoveredTool(tool)">
                                </IconAndCounterCard>
                            </Col>

                        </Row>

                        <Row :gutter="12">
                            <Col :span="24">{{ hoveredTool }}
                                <span v-if="hoveredTool" class="p-3">
                                    {{ hoveredTool.description }}
                                </span>
                            </Col>
                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="selectedTools.length >= 1" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Create Company Button  -->
                            <Button type="primary" size="large" @click="nextStep()"
                                    class="d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Continue</span>
                                <Icon type="ios-arrow-round-forward" />
                            </Button>

                        </focusRipple>
                        
                    </Col>

                    <Col v-if="currentStage == 3" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">Lets Understand Your Business?</h1>
                            <p class="text-center">OQ Suite will make your tools more relevant to your business based on what you do.</p> 
                            <p class="font-weight-bold text-center mb-4">- You can always change this later -</p>  
                        </div>

                        <Row :gutter="12" class="mb-2">

                            <Col :span="24">

                                <el-form :model="ruleForm" :rules="rules" ref="ruleForm" class="demo-ruleForm">

                                    <Row :gutter="12">
                                        <Col :span="8">

                                            <!-- Company logo -->
                                            <imageUploader 
                                                uploadMsg="Upload Company logo"
                                                :thumbnailStyle="{ width:'200px', height:'auto' }"
                                                :allowUpload="true"
                                                :multiple="false"
                                                :imageList="[]">
                                            </imageUploader>

                                        </Col>
                                        <Col :span="16">
                                            <Row :gutter="12">
                                                <Col :span="12">
                                                    <!-- Company Name -->
                                                    <el-form-item label="" prop="company_name" class="mb-0">
                                                        <el-input v-model="ruleForm.company_name" placeholder="Company Name" size="mini"></el-input>
                                                    </el-form-item>
                                                </Col>
                                                <Col :span="12">
                                                    <!-- Company Short Name -->
                                                    <el-form-item label="" prop="company_short_name" class="mb-0">
                                                        <el-input v-model="ruleForm.company_short_name" placeholder="Abbreviation (Optional)" max="10" size="mini"></el-input>
                                                    </el-form-item>
                                                </Col>
                                            </Row>
                                            <Row :gutter="12">
                                                <Col :span="24">
                                                    <!-- Company Email -->
                                                    <el-form-item label="" prop="company_email" class="mb-0">
                                                        <el-input v-model="ruleForm.company_email" placeholder="Company Email" size="mini"></el-input>
                                                    </el-form-item>
                                                    <!-- Company Address -->
                                                    <el-form-item label="" prop="company_address" class="mb-0">
                                                        <el-input v-model="ruleForm.company_address" size="mini" style="width:100%" placeholder="Company Physical Address"></el-input>
                                                    </el-form-item>
                                                    <!-- Gender Selector -->
                                                    <industrySelector
                                                        :selectedIndustry="ruleForm.company_industry"
                                                        @updated="updateGenderChanges($event)">
                                                    </industrySelector>


                                            <el-form-item label="" prop="company_phones" class="mb-2">
                                                <!-- Calling Codes Selector -->
                                                <span class="form-label mb-1 d-block" style="font-size:13px;">Company Phone(s):</span>
                                                <phoneInput 
                                                            class="mb-2"  
                                                            :phones="ruleForm.company_phones" 
                                                            :suggestedPhones="{}"
                                                            :numberLimit="3"
                                                            :selectedType="null"
                                                            :disabledTypes="[]"                                                        
                                                            :removable="true"
                                                            :deletable="false"
                                                            :hidedable="false"
                                                            :editable="true"
                                                            :showPhoneType="true"
                                                            :removeDuplicates="true"
                                                            :showExistingPhonesTab="false"
                                                            :showIcon="false" 
                                                            onIcon="" offIcon="" 
                                                            title="" onText="" offText="" 
                                                            poptipMsg=""
                                                            @updated="ruleForm.company_phones = $event">
                                                </phoneInput>  
                                            </el-form-item>

                                                </Col>
                                            </Row>
                                        </Col>
                                    </Row>

                                    <Row :gutter="12">
                                        <Col :span="24">

                                            
                                            <div v-show="isCreatingCompany" class="mt-4">
                                                <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                                                Creating company...
                                            </div>

                                        </Col>
                                    </Row>

                                </el-form>

                            </Col>

                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="selectedTools.length >= 1" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Create Company Button  -->
                            <Button type="primary" size="large" @click="nextStep()"
                                    class="d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Create My Company</span>
                            </Button>

                        </focusRipple>
                        
                    </Col>

                    <Col v-if="currentStage == 4" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">Go Mobile & Stay Connected!</h1>
                            <p class="text-center">OQ Suite also works offline via SMS but this requires your mobile number. You can use this number to receive notifications, reports as well as connect to mobile money services e.g) Orange Money & MyZaka to pay or receive payments.</p> 
                            <p class="font-weight-bold text-center mb-4">- You can always change this later -</p>  
                        </div>

                        <Row :gutter="12" class="mb-2">

                            <Col :span="24">
                                <Row :gutter="12">
                                    <Col :span="14" :offset="5">
                                        <!-- Calling Codes Selector -->
                                        <span class="form-label mb-1 d-block" style="font-size:13px;">My Mobile Number(s):</span>
                                        <phoneInput 
                                                    class="mb-2"  
                                                    :phones="ruleForm.company_phones" 
                                                    :suggestedPhones="{}"
                                                    :numberLimit="3"
                                                    selectedType="Mobile"
                                                    :disabledTypes="['fax', 'tel']"                                                        
                                                    :removable="true"
                                                    :deletable="false"
                                                    :hidedable="false"
                                                    :editable="true"
                                                    :showPhoneType="true"
                                                    :removeDuplicates="true"
                                                    :showExistingPhonesTab="false"
                                                    :showIcon="false" 
                                                    onIcon="" offIcon="" 
                                                    title="" onText="" offText="" 
                                                    poptipMsg=""
                                                    @updated="ruleForm.company_phones = $event">
                                        </phoneInput> 
                                    </Col>
                                </Row>

                                <Row :gutter="12">
                                    <Col :span="24">

                                        
                                        <div v-show="isCreatingCompany" class="mt-4">
                                            <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                                            Saving...
                                        </div>

                                    </Col>
                                </Row>

                            </Col>

                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="selectedTools.length >= 1" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Add Mobile Number Button  -->
                            <Button type="primary" size="large" @click="nextStep()"
                                    class="d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Add Mobile Number</span>
                            </Button>

                        </focusRipple>
                        
                    </Col>
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Modal Structure  */
    import mainModal from './main.vue';

    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Cards  */
    import IconAndCounterCard from './../cards/IconAndCounterCard.vue';

    /*  Inputs   */
    import phoneInput from './../inputs/phoneInput.vue'; 

    /*  Selectors   */
    import industrySelector from './../../../components/_common/selectors/industrySelector.vue'; 

    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';

    export default {
        components: { mainModal, focusRipple, IconAndCounterCard, phoneInput, industrySelector, imageUploader  },
        data(){
            return{
                //  General data
                currentStage: 1,
                hideModal: false,

                //  Stage 2 data
                hoveredTool: null,
                availableTools: [
                        {
                            name: 'Accounts & Billing',
                            icon: 'ios-cash-outline',
                            description: 'Allows you to create, save and send invoices, quotations and receipts via SMS/Email to your clients. Your client will also be able to pay using their phones as well as view their receipts.',
                        },
                        {
                            name: 'Appointments',
                            icon: 'ios-calendar-outline',
                            description: 'Allows you to create, save and send appointments to your clients via SMS/Email to your clients. Your client will also be able to accept or request appointment reschedules as well as view all appointments in the past using their phone',
                        },
                        {
                            name: 'Jobcard Management',
                            icon: 'ios-briefcase-outline',
                            description: 'Easily track and update jobcards with automation to alert clients via SMS/Email when jobs are completed. Your client will also be able to submit new jobs as well as track the progress of existing ones.',
                        }
                    ],
                selectedTools: [],

                //  Stage 3 data
                ruleForm: {
                    company_name: '',
                    company_short_name: '',
                    company_email: '',
                    company_phones: [],
                    company_address: '',
                },

                //  Form validation rules
                rules: {
                    company_name: [
                        { required: true, message: '', trigger: 'blur' }
                    ],
                    company_email: [
                        { required: true, message: '', trigger: 'blur' }
                    ],
                    company_phones: [
                        { required: true, message: '', trigger: 'blur' }
                    ]
                },

                isCreatingCompany: false
            }
        },
        methods: {
            toggleOption(name, index){
                if( this.selectedTools.includes(name) ){
                    
                    var index;
                    
                    for(var x = 0; x < this.selectedTools.length; x++){
                        if( this.selectedTools[x] == name ){
                            index = x;
                        }
                    }

                    if( this.selectedTools.length == 1 ){
                        this.$Notice.warning({
                            desc: 'You must have atleast one option selected'
                        });
                    }else{
                        this.selectedTools.splice(index, 1);
                    }
                    
                }else{
                    this.selectedTools.push(name);
                }
            },
            showStep2Phones(){
                this.ruleForm.company_name
            },
            nextStep(){
                this.currentStage = this.currentStage + 1;
            },
            updateHoveredTool(tool){
                console.log(tool);
                this.hoveredTool = tool;
            },
            closeModal(){
                this.hideModal = true;
            }
        },
        created(){
            /*
            //  initialize the tour instance
            var enjoyhint_instance = new this.$enjoyhint;

            //  Set the tour instructions
            var enjoyhint_script_steps = [
                    {
                        'click .button_1' : 'Click the "New" button to start creating your project'
                    }
                ];

            //  Set the instructions to the tour instance
            enjoyhint_instance.set(enjoyhint_script_steps);

            //  Start the tour
            enjoyhint_instance.run();
            */
        }
    }
</script>