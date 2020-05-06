<template>
    <div>
        <!-- Modal -->
        <mainModal 
            okText="" 
            :width="750"
            v-bind="$props"
            :isSaving="false" 
            cancelText="Done"
            :hideModal="hideModal"
            title="Add Validation Rule"
            @visibility="$emit('visibility', $event)">

            <template slot="content">
                    
                <!-- Header -->
                <Row :gutter="4" class="bg-grey-light m-0 p-2">
                    
                    <Col :span="10">
                        <span class="font-weight-bold text-dark">Rule</span>
                    </Col>
                    
                    <Col :span="14">
                        <span class="font-weight-bold text-dark">Example Disclaimer</span>
                    </Col>

                </Row>

                <!-- Selected Validation Rules -->
                <div :style="{ maxHeight:'250px', overflowY:'scroll', overflowX:'hidden' }" class="border mb-3 p-2">

                    <!-- Rules -->
                    <Row :gutter="4" class="mx-0 mt-0 mb-2" v-for="(validation_rule, index) in validation_rules" :key="index">

                        <!-- Validation Name -->
                        <Col :span="10">
                            
                            <span>{{ validation_rule.name }}</span>
                        
                        </Col>
                        
                        <!-- Validation Error Message -->    
                        <Col :span="10">

                            <Input v-model="validation_rule.error_msg" type="text" :disabled="true"></Input>
                        
                        </Col>
                                
                        <Col :span="4">

                            <!-- Add Rule Button  -->
                            <Button @click.native="handleSelectedValidationRule(validation_rule)">
                                <Icon type="ios-add" :size="20" />
                                <span>Add</span>
                            </Button>

                        </Col>

                    </Row>
                
                </div>

            </template>
            
        </mainModal>    
        
    </div>

</template>

<script>

    /*  Main Modal   */
    import mainModal from './../../../../../../../../components/_common/modals/main.vue';

    export default {
        components: { mainModal },
        data(){
            return{
                
                hideModal: false,
                
                validation_rules:[
                    {
                        active: true,
                        rule: '/^[a-zA-Z\s]+$/',
                        name: 'Only Letters',
                        type: 'only_letters',
                        error_msg: 'Please enter letters only (spaces allowed)'
                    },
                    {
                        active: true,
                        rule: '/^[0-9\s]+$/',
                        name: 'Only Numbers',
                        type: 'only_numbers',
                        error_msg: 'Please enter numbers only (spaces allowed)'
                    },
                    {
                        active: true,
                        rule: '/^[a-zA-Z0-9\s]+$/',
                        name: 'Only Letters And Numbers (No Spaces)',
                        type: 'only_letters_and_numbers',
                        error_msg: 'Please enter letters and numbers only (spaces allowed)'
                    },
                    {
                        min: '2',
                        active: true,
                        name: 'Minimum Characters',
                        type: 'minimum_characters',
                        error_msg: 'Please enter 2 or more characters'
                    },
                    {
                        max: '5',
                        active: true,
                        name: 'Maximum Characters',
                        type: 'maximum_characters',
                        error_msg: 'Please enter no more than 5 characters'
                    },
                    {
                        active: true,
                        rule: '//',
                        name: 'Validate Email',
                        type: 'validate_email',
                        error_msg: 'Please provide a valid email address e.g example@gmail.com'
                    },
                    /** ^ - start with a number
                     *  [0-9] - use only digits (you can also use \d)
                     *  {8} - use 8 digits
                     *  $ - End here. Don't add anything after 8 digits.
                     */
                    {
                        active: true,
                        rule: '/^[0-9]{8}$/',
                        name: 'Validate Mobile Number',
                        type: 'validate_mobile_number',
                        error_msg: 'Please provide a valid Botswana phone number e.g 71234567'
                    },
                    {
                        active: true,
                        rule: '/[0-9]{2}\/[0-9]{2}\/[0-9]{2}/',
                        name: 'Validate Date Format (DD/MM/YYYY)',
                        type: 'valiate_date_format',
                        error_msg: 'Please enter a valid date (DD/MM/YYYY) e.g 02/08/2020'
                    },
                    {
                        value: '3',
                        active: true,
                        rule: '//',
                        name: 'Equal To (=)',
                        type: 'equal_to',
                        error_msg: 'Please enter the character 3'
                    },
                    {
                        value: '3',
                        active: true,
                        rule: '//',
                        name: 'Not Equal To',
                        type: 'not_equal_to',
                        error_msg: 'Please enter any character except 3'
                    },
                    {
                        value: '3',
                        active: true,
                        rule: '//',
                        name: 'Less Than (<)',
                        type: 'less_than',
                        error_msg: 'Please enter numbers less than 3'
                    },
                    {
                        value: '3',
                        active: true,
                        rule: '//',
                        name: 'Less Than Or Equal (<=)',
                        type: 'less_than_or_equal',
                        error_msg: 'Please enter numbers less than or equal to 3'
                    },
                    {
                        value: '3',
                        active: true,
                        rule: '//',
                        name: 'Greater Than (>)',
                        type: 'greater_than',
                        error_msg: 'Please enter numbers greater than 3'
                    },
                    {
                        value: '3',
                        active: true,
                        rule: '//',
                        name: 'Greater Than Or Equal (>=)',
                        type: 'greater_than_or_equal',
                        error_msg: 'Please enter numbers greater than or equal to 3'
                    },
                    {
                        min: '1',
                        max: '10',
                        active: true,
                        rule: '//',
                        name: 'In Between (Including Inputs)',
                        type: 'in_between_including',
                        error_msg: 'Please enter numbers between 1 and 10 (including 1 and 10)'
                    },
                    {
                        min: '1',
                        max: '10',
                        active: true,
                        rule: '//',
                        name: 'In Between (Excluding Inputs)',
                        type: 'in_between_excluding',
                        error_msg: 'Please enter numbers between 1 and 10 (excluding 1 and 10)'
                    },
                    {
                        active: true,
                        rule: '//',
                        name: 'No Spaces',
                        type: 'no_spaces',
                        error_msg: 'Do not use spaces'
                    },
                    {
                        active: true,
                        rule: '/[a-zA-Z0-9]+/',
                        name: 'Custom Regex',
                        type: 'custom_regex',
                        error_msg: 'Custom regex validation error'
                    }
                ]

            }
        },
        methods: {
            handleSelectedValidationRule(validation_rule){

                //  Notify the parent component of the selected validation rule
                this.$emit('selected', validation_rule);

                //  Close the modal
                this.closeModal();

            },
            closeModal(){

                this.hideModal = true;

            }
        }
    }
</script>