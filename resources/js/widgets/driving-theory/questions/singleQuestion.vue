<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Question Toolbox */

    .question-option >>> .question-toolbox{
        z-index: 1;
        position: relative;
        margin: -2px 0 0 0;
        background: #fff;
    }

    .question-option >>> .question-toolbox,
    .question-option >>> .question-toolbox .hidable{
        opacity:0;
    }

    .question-option:hover >>> .question-toolbox,
    .question-option:hover >>> .question-toolbox .hidable{
        opacity:1;
    }

    .question-option >>> .question-toolbox .question-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .question-option >>> .question-toolbox .question-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .question-option >>> .ivu-card-body{
        padding:0 !important;
    }

    .question-details{
        padding:16px;
    }

</style>

<template>

    <Card v-if="localQuestion" class="question-option mb-2">

        <!-- Question Name -->
        <div slot="title">

            <!-- Question Name Label  -->
            <span class="question-name font-weight-bold cut-text">
                {{ getQuestionNumber ? getQuestionNumber +'. ' : '' }}
                {{ localQuestion.text }}
            </span>
            
        </div>

        <!-- Question Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: -2px;">

            <span class="d-flex blue-highlighter mr-2" style="border-radius: 5px;">
                <!-- Question Type -->
                <span>{{ localQuestion.choices.length }}</span>

            </span>

            <div class="question-toolbox">

                <!-- Remove Question Button  -->
                <Poptip confirm title="Are you sure you want to remove this question?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveQuestion(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="question-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Question Button  -->
                <Icon type="ios-create-outline" class="question-icon hidable mr-2" size="20" @click="editQuestion()" />

                <!-- Move Question Button  -->
                <Icon type="ios-move" class="question-icon question-dragger-handle hidable mr-2" size="20" />
            
            </div>

        </div>   

        <!-- Display Choices -->
        <div>

            <div v-for="(choice, index) in localQuestion.choices">

                <span>{{ choice.text }}</span>

            </div>

        </div>
    
        <!-- 
            MODAL TO EDIT EXISTING QUESTION
        -->
        <editQuestionModal 
            :question="localQuestion"
            v-if="isOpenEditQuestionModal" 
            @visibility="isOpenEditQuestionModal = $event">
        </editQuestionModal> 

    </Card>

</template>

<script>

    //  Get the create new question modal
    import editQuestionModal from './edit/editQuestionModal.vue';

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            topic: {
                type: Object,
                default:() => {}
            },
            question: {
                type: Object,
                default:() => {}
            }
        }, 
        components: { editQuestionModal },
        data(){
            return {
                localQuestion: this.question,
                isOpenEditQuestionModal: false
            }
        },
        watch: {
            //  Keep track of changes on the question
            question: {

                handler: function (val, oldVal) {
                    
                    /*  Update the localScreen  */
                    this.localQuestion = val;

                },
                deep: true

            },
        },
        computed: {
            getQuestionNumber(){
                /**
                 *  Returns the question number. We use this as we list the questions.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            }
        },
        methods: {
            editQuestion(){
                this.isOpenEditQuestionModal = true;
            },
            handleDuplicateQuestion(index) {

                //  Duplicate the question
                var duplicateQuestion = _.cloneDeep( this.topic.questions[index] );

                //  Create the duplicate question name
                var duplicateName = 'Question - #' + (this.topic.questions.length + 1);

                //  Set the duplicate question name
                duplicateQuestion.name = duplicateName;

                //  Add the duplicate question to the rest of the other questions
                this.topic.questions.push(duplicateQuestion);

            },
            handleRemoveQuestion(index) {
                this.topic.questions.splice(index, 1);
            }
        }
    }

</script>