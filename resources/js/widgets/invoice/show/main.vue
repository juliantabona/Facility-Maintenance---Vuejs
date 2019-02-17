<style scoped>

    .invoice-widget{
        position: relative;
    }

    .fade-enter,
    .fade-leave-active {
        opacity: 0;
        transform: translateX(50px);
    }
    .fade-leave-active {
        position: absolute;
    }
 
    .animated {
        transition: all 0.5s;
        display: flex;
        width: 100%;
    }

</style>

<template>

    <div id="invoice-widget" style="overflow:hidden;">

        <!-- Get the summary header to display the invoice #, status, due date, amount due and menu options -->
        <overview 
            v-if="!createMode && localInvoice.has_approved"
            :invoice="localInvoice" :editMode="editMode" :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving invoice -->
        <Row>
            <Col :span="24">
                <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        <transition-group name="fade">
            
            <Row :gutter="20" key="recurring_details" class="animated">

                <!-- Recurring toggle switch, Recurring settings toolbox, Save changes button -->
                <Col :span="24">

                    <!-- Save changes button -->
                    <saveInvoiceBtn  v-if="!createMode && invoiceHasChanged" 
                                    class="float-right pt-2 ml-4" :style="{ position:'relative' }"
                                    type="success" size="small" 
                                    :ripple="true"
                                    @click.native="saveInvoice()">
                    </saveInvoiceBtn>

                    <!-- Recurring Settings Icon Button -->
                    <span class="float-right d-block pt-2">
                        <div @click="showRecurringSettings = !showRecurringSettings" :style="{ position: 'relative', zIndex: '1' }">
                            <Icon :style="showRecurringSettings ? { fontSize: '20px',height: '33px',color: '#2d8cf0',background: '#eee',borderRadius: '50% 50% 0 0',padding: '3px 6px',marginTop: '-3px',boxShadow: '#c8c8c8 1px 1px 1px inset',cursor: 'pointer' }: { cursor: 'pointer' }"
                                type="ios-settings-outline" :size="20" />
                        </div>
                    </span>

                    <!-- Make recurring switch -->
                    <toggleSwitch v-bind:toggleValue.sync="localInvoice.isRecurring" 
                        @update:toggleValue="updateReccuring($event)"
                        :ripple="false" :showIcon="true" onIcon="ios-eye-off-outline" offIcon="ios-eye-outline" 
                        title="Make Recurring:" onText="Yes" offText="No" poptipMsg="Turn on to make recurring"
                        class="float-right p-2">
                    </toggleSwitch>

                    <!-- Make recurring settings -->
                    <Row v-show="showRecurringSettings" key="dynamic" class="animated mb-3">

                        <!-- White overlay when creating/saving invoice -->
                        <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                        <Col span="24">
                            <div style="background:#eee;padding: 20px">
                                <Card :bordered="false">
                                    <Row>
                                        <Col span="24" class="mb-3">
                                            <h6>
                                                <Icon type="ios-information-circle-outline" :size="24" :style="{ marginTop:'-3px' }" />
                                                <span class="font-weight-bold">Recurring Settings:</span>
                                                <Alert class="mt-2 mb-1" :style="{ zIndex:'1' }">
                                                    Schedule how your invoices will be sent to your customer over time.
                                                </Alert>
                                            </h6>
                                        </Col>

                                        <!-- Reminders - Before Due Date  -->
                                        <Col span="24">
                                        
                                            <!-- Reminder method Selector -->
                                            <span class="float-left d-block mr-1 mb-3">Repeat this invoice</span>

                                            <!-- Timeline Options -->
                                            <Select v-model="localInvoice.recurringSchedule.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select frequency">
                                                <Option value="Daily">Daily</Option>
                                                <Option value="Weekly">Weekly</Option>
                                                <Option value="Monthly">Monthly</Option>
                                                <Option value="Yearly">Yearly</Option>
                                                <Option value="Custom">Custom</Option>
                                            </Select>

                                            <!-- If Weekly -->
                                            <span v-show="localInvoice.recurringSchedule.chosen == 'Weekly'" class="mb-3">
                                                <span class="float-left d-block ml-1 mr-1">every</span>
                                                <Select v-model="localInvoice.recurringSchedule.weekly" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day of week">
                                                    <Option value="Monday">Monday</Option>
                                                    <Option value="Tuesday">Tuesday</Option>
                                                    <Option value="Wednesday">Wednesday</Option>
                                                    <Option value="Thursday">Thursday</Option>
                                                    <Option value="Friday">Friday</Option>
                                                    <Option value="Saturday">Saturday</Option>
                                                    <Option value="Sunday">Sunday</Option>
                                                </Select>
                                            </span>
                                            
                                            <!-- If Monthly -->
                                            <span v-show="localInvoice.recurringSchedule.chosen == 'Monthly'" class="mb-3">
                                                <span class="float-left d-block ml-1 mr-1">on the</span>
                                                <Select v-model="localInvoice.recurringSchedule.monthly" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day">
                                                    <Option value="1st">1st</Option>
                                                    <Option value="2nd">2nd</Option>
                                                    <Option value="3rd">3rd</Option>
                                                    <Option value="4th">4th</Option>
                                                    <Option value="5th">5th</Option>
                                                    <Option value="6th">6th</Option>
                                                    <Option value="7th">7th</Option>
                                                    <Option value="8th">8th</Option>
                                                    <Option value="9th">9th</Option>
                                                    <Option value="10th">10th</Option>
                                                    <Option value="11th">11th</Option>
                                                    <Option value="12th">12th</Option>
                                                    <Option value="13th">13th</Option>
                                                    <Option value="14th">14th</Option>
                                                    <Option value="15th">15th</Option>
                                                    <Option value="16th">16th</Option>
                                                    <Option value="17th">17th</Option>
                                                    <Option value="18th">18th</Option>
                                                    <Option value="19th">19th</Option>
                                                    <Option value="20th">20th</Option>
                                                    <Option value="21st">21st</Option>
                                                    <Option value="22nd">22nd</Option>
                                                    <Option value="23rd">23rd</Option>
                                                    <Option value="24th">24th</Option>
                                                    <Option value="25th">25th</Option>
                                                    <Option value="26th">26th</Option>
                                                    <Option value="27th">27th</Option>
                                                    <Option value="28th">28th</Option>
                                                    <Option value="29th">29th</Option>
                                                    <Option value="30th">30th</Option>
                                                    <Option value="31st">31st</Option>
                                                </Select>
                                                <span class="float-left d-block ml-1 mr-1">day of every month</span>
                                            </span>

                                            <!-- If Yearly -->
                                            <span v-show="localInvoice.recurringSchedule.chosen == 'Yearly'" class="mb-3">
                                                <span class="float-left d-block ml-1 mr-1">every</span>
                                                <Select v-model="localInvoice.recurringSchedule.yearly.month" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day of week">
                                                    <Option value="January">January</Option>
                                                    <Option value="February">February</Option>
                                                    <Option value="March">March</Option>
                                                    <Option value="April">April</Option>
                                                    <Option value="May">May</Option>
                                                    <Option value="June">June</Option>
                                                    <Option value="July">July</Option>
                                                    <Option value="August">August</Option>
                                                    <Option value="September">September</Option>
                                                    <Option value="October">October</Option>
                                                    <Option value="November">November</Option>
                                                    <Option value="December">December</Option>
                                                </Select>
                                                
                                                <span class="float-left d-block ml-1 mr-1">on the</span>
                                                <Select v-model="localInvoice.recurringSchedule.yearly.day" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day">
                                                    <Option value="1st">1st</Option>
                                                    <Option value="2nd">2nd</Option>
                                                    <Option value="3rd">3rd</Option>
                                                    <Option value="4th">4th</Option>
                                                    <Option value="5th">5th</Option>
                                                    <Option value="6th">6th</Option>
                                                    <Option value="7th">7th</Option>
                                                    <Option value="8th">8th</Option>
                                                    <Option value="9th">9th</Option>
                                                    <Option value="10th">10th</Option>
                                                    <Option value="11th">11th</Option>
                                                    <Option value="12th">12th</Option>
                                                    <Option value="13th">13th</Option>
                                                    <Option value="14th">14th</Option>
                                                    <Option value="15th">15th</Option>
                                                    <Option value="16th">16th</Option>
                                                    <Option value="17th">17th</Option>
                                                    <Option value="18th">18th</Option>
                                                    <Option value="19th">19th</Option>
                                                    <Option value="20th">20th</Option>
                                                    <Option value="21st">21st</Option>
                                                    <Option value="22nd">22nd</Option>
                                                    <Option value="23rd">23rd</Option>
                                                    <Option value="24th">24th</Option>
                                                    <Option value="25th">25th</Option>
                                                    <Option value="26th">26th</Option>
                                                    <Option value="27th">27th</Option>
                                                    <Option value="28th">28th</Option>
                                                    <Option value="29th">29th</Option>
                                                    <Option value="30th">30th</Option>
                                                    <Option value="31st">31st</Option>
                                                </Select>
                                            </span>

                                            <!-- If Custom -->
                                            <span v-show="localInvoice.recurringSchedule.chosen == 'Custom'" class="mb-3">
                                                <span class="float-left d-block ml-1 mr-1 mb-3">every</span>
                                                <el-input v-model="localInvoice.recurringSchedule.custom.count" placeholder="E.g) 6" size="mini" class="float-left d-block mr-1 mb-3" :style="{ maxWidth:'60px', marginTop:'-3px' }"></el-input>
                                                <Select v-model="localInvoice.recurringSchedule.custom.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week">
                                                    <Option value="Days">Day(s)</Option>
                                                    <Option value="Weeks">Week(s)</Option>
                                                    <Option value="Months">Month(s)</Option>
                                                    <Option value="Years">Year(s)</Option>
                                                </Select>

                                                <!-- If weeks -->
                                                <span v-show="localInvoice.recurringSchedule.custom.chosen == 'Weeks'">
                                                    <span class="float-left d-block ml-1 mr-1 mb-3">on every</span>
                                                    <Select v-model="localInvoice.recurringSchedule.custom.weeks" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week">
                                                        <Option value="Monday">Monday</Option>
                                                        <Option value="Tuesday">Tuesday</Option>
                                                        <Option value="Wednesday">Wednesday</Option>
                                                        <Option value="Thursday">Thursday</Option>
                                                        <Option value="Friday">Friday</Option>
                                                        <Option value="Saturday">Saturday</Option>
                                                        <Option value="Sunday">Sunday</Option>
                                                    </Select>
                                                </span>
                                                
                                                <!-- If months -->
                                                <span v-show="localInvoice.recurringSchedule.custom.chosen == 'Months'">
                                                    <span class="float-left d-block ml-1 mr-1 mb-3">on the</span>
                                                        <Select v-model="localInvoice.recurringSchedule.custom.months" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day">
                                                            <Option value="1st">1st</Option>
                                                            <Option value="2nd">2nd</Option>
                                                            <Option value="3rd">3rd</Option>
                                                            <Option value="4th">4th</Option>
                                                            <Option value="5th">5th</Option>
                                                            <Option value="6th">6th</Option>
                                                            <Option value="7th">7th</Option>
                                                            <Option value="8th">8th</Option>
                                                            <Option value="9th">9th</Option>
                                                            <Option value="10th">10th</Option>
                                                            <Option value="11th">11th</Option>
                                                            <Option value="12th">12th</Option>
                                                            <Option value="13th">13th</Option>
                                                            <Option value="14th">14th</Option>
                                                            <Option value="15th">15th</Option>
                                                            <Option value="16th">16th</Option>
                                                            <Option value="17th">17th</Option>
                                                            <Option value="18th">18th</Option>
                                                            <Option value="19th">19th</Option>
                                                            <Option value="20th">20th</Option>
                                                            <Option value="21st">21st</Option>
                                                            <Option value="22nd">22nd</Option>
                                                            <Option value="23rd">23rd</Option>
                                                            <Option value="24th">24th</Option>
                                                            <Option value="25th">25th</Option>
                                                            <Option value="26th">26th</Option>
                                                            <Option value="27th">27th</Option>
                                                            <Option value="28th">28th</Option>
                                                            <Option value="29th">29th</Option>
                                                            <Option value="30th">30th</Option>
                                                            <Option value="31st">31st</Option>
                                                    </Select>
                                                    <span class="float-left d-block ml-1 mr-1 mb-3">day of every month</span>
                                                </span>

                                                <!-- If years -->
                                                <span v-show="localInvoice.recurringSchedule.custom.chosen == 'Years'">
                                                    <span class="float-left d-block ml-1 mr-1 mb-3">on every</span>
                                                    <Select v-model="localInvoice.recurringSchedule.custom.years.month" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week">
                                                        <Option value="January">January</Option>
                                                        <Option value="February">February</Option>
                                                        <Option value="March">March</Option>
                                                        <Option value="April">April</Option>
                                                        <Option value="May">May</Option>
                                                        <Option value="June">June</Option>
                                                        <Option value="July">July</Option>
                                                        <Option value="August">August</Option>
                                                        <Option value="September">September</Option>
                                                        <Option value="October">October</Option>
                                                        <Option value="November">November</Option>
                                                        <Option value="December">December</Option>
                                                    </Select>
                                                    <span class="float-left d-block ml-1 mr-1 mb-3">on the</span>
                                                    <Select v-model="localInvoice.recurringSchedule.custom.years.day" :style="{ width:'60px', marginTop:'-5px' }" class="float-left mb-3" placeholder="Select day">
                                                        <Option value="1st">1st</Option>
                                                        <Option value="2nd">2nd</Option>
                                                        <Option value="3rd">3rd</Option>
                                                        <Option value="4th">4th</Option>
                                                        <Option value="5th">5th</Option>
                                                        <Option value="6th">6th</Option>
                                                        <Option value="7th">7th</Option>
                                                        <Option value="8th">8th</Option>
                                                        <Option value="9th">9th</Option>
                                                        <Option value="10th">10th</Option>
                                                        <Option value="11th">11th</Option>
                                                        <Option value="12th">12th</Option>
                                                        <Option value="13th">13th</Option>
                                                        <Option value="14th">14th</Option>
                                                        <Option value="15th">15th</Option>
                                                        <Option value="16th">16th</Option>
                                                        <Option value="17th">17th</Option>
                                                        <Option value="18th">18th</Option>
                                                        <Option value="19th">19th</Option>
                                                        <Option value="20th">20th</Option>
                                                        <Option value="21st">21st</Option>
                                                        <Option value="22nd">22nd</Option>
                                                        <Option value="23rd">23rd</Option>
                                                        <Option value="24th">24th</Option>
                                                        <Option value="25th">25th</Option>
                                                        <Option value="26th">26th</Option>
                                                        <Option value="27th">27th</Option>
                                                        <Option value="28th">28th</Option>
                                                        <Option value="29th">29th</Option>
                                                        <Option value="30th">30th</Option>
                                                        <Option value="31st">31st</Option>
                                                    </Select>
                                                </span>
                                            </span>
                                        </Col>

                                        <!-- Reminders - Before Due Date  -->
                                        <Col span="24" class="border-top pt-3">
                                        
                                            <!-- Text for when to create first invoice -->
                                            <span class="float-left d-block mr-1 mb-3">Create first invoice on</span>

                                            <!-- First Invoice - Start Date -->
                                            <el-date-picker v-model="localInvoice.recurringSchedule.start" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="float-left mb-2" :style="{ maxWidth:'135px', marginTop:'-3px' }"
                                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                                            </el-date-picker>

                                            <!-- Text for when to end -->
                                            <span class="float-left d-block mr-1 ml-1 mb-3">and end</span>

                                            <!-- Text for when to end -->
                                            <Select v-model="localInvoice.recurringSchedule.stop.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left mb-3" placeholder="Select day of week">
                                                <Option value="After">After</Option>
                                                <Option value="On">On</Option>
                                                <Option value="Never">Never</Option>
                                            </Select>
                                            <el-input v-show="localInvoice.recurringSchedule.stop.chosen == 'After'" v-model="localInvoice.recurringSchedule.stop.count" placeholder="E.g) 3" size="mini" class="float-left mr-1 ml-1 mb-3" :style="{ maxWidth:'80px', marginTop:'-3px' }"></el-input>
                                            <el-date-picker v-show="localInvoice.recurringSchedule.stop.chosen == 'On'" v-model="localInvoice.recurringSchedule.stop.day" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="float-left mr-1 ml-1 mb-3" :style="{ maxWidth:'135px', marginTop:'-3px' }"
                                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                                            </el-date-picker>

                                        </Col>

                                    </Row>
                                </Card>
                            </div>
                        </Col>
                    </Row>

                </Col>

            </Row>

            <!-- Activity cards & Invoice Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated">
                <!-- White overlay when creating/saving invoice -->
                <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                <!-- Acitvity cards for showing summary of activities, sent invoices, and sent receipt -->
                <Col v-if="localInvoice.has_approved" :span="5">
                
                    <!-- Activity Number Card -->
                    <IconAndCounterCard title="Activity" icon="ios-pulse-outline" :count="localInvoice.activity_count.total" class="mb-2" type="success"
                                        :route="{ name: 'show-invoice-activities', params: { id: localInvoice.id } }">
                    </IconAndCounterCard>

                    <!-- Sent Incoices Number Card -->
                    <IconAndCounterCard title="Sent Invoices" icon="ios-send-outline" :count="localInvoice.sent_invoice_activity_count.total" class="mb-2"
                                        :route="{ name: 'show-invoice-activities', params: { id: localInvoice.id } , query: { activity_type: 'sent' } }">
                    </IconAndCounterCard>

                    <!-- Sent Recipts Number Card -->
                    <IconAndCounterCard title="Sent Receipts" icon="ios-paper-outline" :count="localInvoice.sent_receipt_activity_count.total" class="mb-2"
                                        :route="{ name: 'show-invoice-activities', params: { id: localInvoice.id } , query: { activity_type: 'sent_receipt' } }">
                    </IconAndCounterCard>
                
                </Col>

                <!-- Invoice steps, Approval step, Sending step and Payment step -->
                <Col :span="localInvoice.has_approved ? 19 : 24">
                    <!-- Get the staging toolbar to display the invoice approved, sent/re-send and record payment stages -->
                    <steps 
                        v-if="!createMode"
                        :invoice="localInvoice" :editMode="editMode" :createMode="createMode" 
                        @toggleEditMode="toggleEditMode($event)" @approved="updateInvoiceData($event)" 
                        @sent="updateInvoiceData($event)" @skipped="updateInvoiceData($event)"
                        @paid="updateInvoiceData($event)" @cancelled="updateInvoiceData($event)" 
                        @reminderSet="updateInvoiceData($event)">
                    </steps>
                </Col>

            </Row>

            <!-- Loaders for creating/saving invoice -->
            <Row key="create_n_save_loaders" class="animated">
                <Col :span="24">
                    <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                    <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
                </Col>
            </Row>

            <!-- Invoice View/Editor -->
            <Row id="invoice-summary-1"  key="invoice_template" class="animated">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving invoice -->
                        <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                        <!-- Main header -->
                        <div slot="title">
                            <h5>Invoice Summary</h5>
                        </div>

                        <!-- Invoice options -->
                        <div slot="extra" v-if="showMenuBtn && !createMode">
                            
                            <mainHeader :invoice="localInvoice" :editMode="editMode" @toggleEditMode="toggleEditMode($event)"></mainHeader>

                        </div>

                        <Row>

                            <Col span="24" class="pr-4">

                                <!-- Create button -->
                                <createInvoiceBtn v-if="createMode" 
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="createInvoice()">
                                </createInvoiceBtn>

                                <!-- Save changes button -->
                                <saveInvoiceBtn  v-if="!createMode && invoiceHasChanged" 
                                                class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="saveInvoice()">
                                </saveInvoiceBtn>

                                <!-- Edit mode switch -->
                                <editModeSwitch v-bind:editMode.sync="editMode" :ripple="false" class="float-right mb-2"></editModeSwitch>

                                <div class="clearfix"></div>

                            </Col>

                            <Col span="12">

                                <!-- Company logo -->
                                <imageUploader 
                                    uploadMsg="Upload or change logo"
                                    :thumbnailStyle="{ width:'200px', height:'auto' }"
                                    :allowUpload="editMode"
                                    :multiple="false"
                                    :imageList="
                                        [{
                                            'name': 'Company Logo',
                                            'url': 'https://wave-prod-accounting.s3.amazonaws.com/uploads/invoices/business_logos/7cac2c58-4cc1-471b-a7ff-7055296fffbc.png'
                                        }]">
                                </imageUploader>
                            </Col>

                            <Col v-if="company" span="12" class="pr-4">
                                
                                <!-- Invoice Title -->
                                <mainTitle :invoice="localInvoice" :editMode="editMode"></mainTitle>
                                
                                <!-- Company information -->
                                <companyOrIndividualDetails 
                                    :client="localInvoice.customized_company_details" :editMode="editMode" align="right"
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="false"
                                    @updated:companyOrIndividual="updateCompany($event)"
                                    @updated:phones="updatePhoneChanges(localInvoice.customized_company_details, $event)"
                                    @reUpdateParent="storeOriginalInvoice()">
                                </companyOrIndividualDetails>

                            </Col>

                        </Row>

                        <Divider dashed class="mt-3 mb-3" />

                        <Row>
                            <Col span="12" class="pl-2">
                                <h3 v-if="!editMode" class="text-dark mb-3">{{ localInvoice.invoice_to_title ? localInvoice.invoice_to_title+':' : '' }}</h3>
                                <el-input v-if="editMode" placeholder="Invoice heading" v-model="localInvoice.invoice_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>

                                <!-- Client information -->
                                <companyOrIndividualDetails 
                                    :client="localInvoice.customized_client_details" :editMode="editMode"
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="true"
                                    @updated:companyOrIndividual="updateClient($event)"
                                    @updated:phones="updatePhoneChanges(localInvoice.customized_client_details, $event)"
                                    @reUpdateParent="storeOriginalInvoice()">
                                </companyOrIndividualDetails>

                                <!-- Client selector -->
                                <clientSelector :style="{maxWidth: '250px'}" class="mt-2"
                                    @updated="changeClient($event)">
                                </clientSelector>

                            </Col>
                            
                            <Col span="12">
                                <!-- Invoice details e.g) Reference #, created date, due date, grand total -->
                                <summaryDetails :invoice="localInvoice" :editMode="editMode" :createMode="createMode"></summaryDetails>
                            </Col>
                        
                            <Col span="24">
                                <!-- Edit mode toolbar e.g) Currency selector, primary/secondary color picker -->
                                <toolbar v-if="editMode" :invoice="localInvoice" :editMode="editMode" class="mt-2"></toolbar>
                            </Col>

                            <!-- Invoice list items (products/services) -->
                            <Col span="24">
                                <items :invoice="localInvoice" :editMode="editMode"></items>
                            </Col>

                        </Row>

                        <Divider dashed class="mt-0 mb-4" />

                        <!-- Total details e.g) Sub/grand total and tax amounts -->
                        <Row>
                            <Col span="12" offset="12" class="pr-4">
                                <totalBreakDown :invoice="localInvoice" :editMode="editMode"></totalBreakDown>
                            </Col>
                            <Col span="24">
                                <!-- Invoice footer notes e.g) For noting payment details/terms and conditions -->
                                <notes :invoice="localInvoice" :editMode="editMode"></notes>
                            </Col>

                        </Row>

                        <!-- Invoice page footer -->
                        <mainFooter :invoice="localInvoice" :editMode="editMode"></mainFooter>

                    </Card>
                </Col>
            </Row>

        </transition-group>

    </div>

</template>

<script>

    /*  Local components    */
    import overview from './overview.vue';
    import steps from './steps.vue';
    import mainHeader from './header.vue';
    import mainTitle from './title.vue';
    import companyOrIndividualDetails from './companyOrIndividualDetails.vue';
    import summaryDetails from './details.vue';
    import toolbar from './toolbar.vue';
    import items from './items.vue';
    import totalBreakDown from './totalBreakDown.vue';
    import notes from './notes.vue';
    import mainFooter from './footer.vue';
    
    /*  Buttons  */
    import createInvoiceBtn from './../../../components/_common/buttons/createInvoiceBtn.vue';
    import saveInvoiceBtn from './../../../components/_common/buttons/saveInvoiceBtn.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';

    /*  Selectors  */
    import clientSelector from './../../../components/_common/selectors/clientSelector.vue';   

    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';
    

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, 
            mainTitle, companyOrIndividualDetails, summaryDetails, toolbar,
            items, totalBreakDown, notes, mainFooter,
            createInvoiceBtn, saveInvoiceBtn, toggleSwitch, editModeSwitch,
            Loader, imageUploader, clientSelector, IconAndCounterCard
        },
        props: {
            invoice: {
                type: Object,
                default: function () { 
                    return {
                        status: '',
                        heading: '',
                        invoice_to_title: '',
                        reference_no_title: '',
                        reference_no_value: '',
                        created_date_title: '',
                        created_date_value: '',
                        expiry_date_title: '',
                        expiry_date_value: '',
                        sub_total_title: '',
                        sub_total_value: 0,
                        grand_total_title: '',
                        grand_total_value: 0,
                        currency_type: null,
                        customized_company_details: null,
                        customized_client_details: null,
                        client_id: null,
                        calculated_taxes: [],
                        table_columns: [],
                        items: [],
                        notes: {
                            title: '',
                            details: ''
                        },
                        colors: [],
                        footer: ''
                    }
                }
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            create: {
                type: Boolean,
                default: false
            },
            modelType:{
                type: String,
                default: ''
            },
            modelId:{
                type: Number,
                default: null
            }
        },
        data(){
            return {

                showRecurringSettings: false,

                user: auth.user,

                //  Modes
                editMode: false,
                createMode: this.create,

                //  Loading States
                isSavingInvoice: false,
                isCreatingInvoice: false,

                //  Local Invoice and state changes
                localInvoice: (this.invoice || {}),
                _localInvoiceBeforeChange: {},
                invoiceHasChanged: false,

                //  Invoice Shorthands
                company: this.invoice.customized_company_details,
                client: this.invoice.customized_client_details,
                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,
                
            }
        },
        watch: {
            localInvoice: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfinvoiceHasChanged - 1');
                    this.invoiceHasChanged = this.checkIfinvoiceHasChanged(val);
                },
                deep: true
            }
        },
        methods: {
            toggleEditMode(activate = true){

                var self = this,
                    options = {
                        easing: 'ease-in-out',
                        offset: -100,
                        force: true,
                        cancelable: true,
                        onStart: function(element) {
                            // scrolling started
                        },
                        onDone: function(element) {
                            //  Activate edit mode
                            self.editMode = activate;
                        },
                        onCancel: function() {
                        // scrolling has been interrupted
                        },
                        x: false,
                        y: true
                    }

                //var cancelScroll = VueScrollTo.scrollTo('invoice-summary-1', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#invoice-summary-1', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updateReccuring(val){
                
                this.localInvoice.isRecurring = val ? 1 : 0;
                
                this.showRecurringSettings = val;
                
            },
            changeClient(newClient){

                if(newClient.model_type == 'user'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.first_name +  ' ' + newClient.last_name
                    });

                }else if(newClient.model_type == 'company'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.name
                    });
                }

                this.client = this.localInvoice.customized_client_details = newClient;
                
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },

            updateClient(newClientDetails){

                this.client = this.localInvoice.customized_client_details = newClientDetails;

                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },

            updateCompany(newCompanyDetails){
                
                this.company = this.localInvoice.customized_company_details = newCompanyDetails;

                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },

            updatePhoneChanges(companyOrIndividual, phones){
                
                companyOrIndividual.phones = phones;
                
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },
            activateCreateMode: function(){
                this.fetchInvoiceTemplate();
            },
            fetchInvoiceTemplate() {
                if(this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingInvoiceTemplate = true;

                    console.log('Start getting invoice template from company settings...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'/settings')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingInvoiceTemplate = false;

                            //  Get currencies
                            var template = (((data || {}).details || {}).invoiceTemplate || null);

                            if(template){
                                //  Activate edit mode
                                self.editMode = true;
                                console.log('Updaing the local invoice with template');
                                console.log(self.localInvoice);
                                self.populateInvoiceTemplate(template);
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoiceTemplate = false;

                            console.log('invoiceSummaryWidget.vue - Error getting invoice template from company settings...');
                            console.log(response);    
                        });
                }
            },
            populateInvoiceTemplate(template){
                console.log('Populating invoice template with deault settings');
                var date = new Date();
                var dd = ('0' + date.getDate()).slice(-2);
                var mm = ('0' + (date.getMonth() + 1)).slice(-2);
                var yy = date.getFullYear();
                
                //  Update Invoice Object Using Template Data

                this.localInvoice.status = template.status;
                this.localInvoice.heading = template.heading;
                this.localInvoice.reference_no_title = template.reference_no_title;
                this.localInvoice.created_date_title = template.created_date_title;
                this.localInvoice.expiry_date_title = template.expiry_date_title;
                this.localInvoice.sub_total_title = template.sub_total_title;
                this.localInvoice.grand_total_title = template.grand_total_title;
                this.localInvoice.currency_type = template.currency_type;
                this.localInvoice.invoice_to_title = template.invoice_to_title;
                this.localInvoice.table_columns = template.table_columns;
                this.localInvoice.items = template.items;
                this.localInvoice.notes = template.notes;
                this.localInvoice.colors = template.colors;
                this.localInvoice.footer = template.footer;

                //  Update Invoice Dates Using Current Dates
                
                this.localInvoice.created_date_value = yy+'-'+mm+'-'+dd;
                this.localInvoice.expiry_date_value = yy+'-'+mm+'-'+('0' + (date.getDate() + 7) ).slice(-2);

                //  Update Invoice Shorthands

                this.currencySymbol = this.localInvoice.currency_type.currency.symbol;
                
                this.fetchCompanyInfo();
            },
            checkIfinvoiceHasChanged: function(updatedInvoice = null){
                
                var currentInvoice = _.cloneDeep(updatedInvoice || this.localInvoice);
                var isNotEqual = !_.isEqual(currentInvoice, this._localInvoiceBeforeChange);

                console.log('currentInvoice');
                console.log(currentInvoice);
                console.log('_localInvoiceBeforeChange');
                console.log(this._localInvoiceBeforeChange);
                console.log('isNotEqual:' +isNotEqual);

                return isNotEqual;
            },
            storeOriginalInvoice(){
                //  Store the original invoice
                this._localInvoiceBeforeChange = _.cloneDeep(this.localInvoice);
                console.log('stored _localInvoiceBeforeChange');
                console.log(this._localInvoiceBeforeChange);
            },
            saveInvoice(){
                
                var self = this;

                //  Start loader
                self.isSavingInvoice = true;

                console.log('Attempt to save invoice...');

                console.log( self.localInvoice );

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log(invoiceData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingInvoice = false;

                        /*
                        *  updateInvoiceData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateInvoiceData(data);

                        //  Alert creation success
                        self.$Message.success('Invoice saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error saving invoice...');
                        console.log(response);
                    });
            },
            createInvoice(){

                var self = this;

                //  Start loader
                self.isCreatingInvoice = true;

                console.log('Attempt to create invoice...');

                console.log( self.localInvoice );

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log(invoiceData);

                var associatedModel = (this.modelType && this.modelId) ? '?model='+this.modelType+'&modelId='+this.modelId: '';
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices'+associatedModel, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingInvoice = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the invoice as the original invoice
                        self.storeOriginalInvoice();
                        
                        console.log('checkIfinvoiceHasChanged - 3');
                        self.invoiceHasChanged = self.checkIfinvoiceHasChanged();

                        //  Alert creation success
                        self.$Message.success('Invoice created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('invoiceCreated', data);

                        //  Go to invoice
                        self.$router.push({ name: 'show-invoice', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error creating invoice...');
                        console.log(response);
                    });
            },
            updateInvoiceData(newInvoice){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newInvoice); x++){
                    var key = Object.keys(this.localInvoice)[x];
                    this.$set(this.localInvoice, key, newInvoice[key]);
                }

                //  Store the current state of the invoice as the original invoice
                this.storeOriginalInvoice();

                console.log('checkIfinvoiceHasChanged - 4');
                //  Update the invoice change status
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },
            fetchCompanyInfo() {
                if(!this.company && this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingCompanyInfo = true;

                    console.log('Start getting company details...');

                    //  Additional data to eager load along with company found
                    var connections = '&connections=phones';
                    
                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'?model=Company'+connections)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingCompanyInfo = false;

                            if(data){
                                //  Format the company details
                                self.company = self.localInvoice.customized_company_details = data;
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingCompanyInfo = false;

                            console.log('invoiceSummaryWidget.vue - Error getting company details...');
                            console.log(response);    
                        });
                }
            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Store the current state of the invoice as the original invoice
                console.log('Now lets store that original invoice!');
                this.storeOriginalInvoice();

                //  Update the invoice change status
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

                if(this.createMode){
                    this.activateCreateMode();
                }

            })
        }
    };
  
</script>