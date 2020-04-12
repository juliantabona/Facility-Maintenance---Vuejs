<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Question Toolbox */

    .draggable-option >>> .question-number{
        color: #fff;
        padding: 10px;
        font-size: 20px;
        background: #6f9cca;
        border-radius: 0 10px;
    }

    .draggable-option >>> .question-text{
        width: 100%;
        align-self: center;
        line-height: 1.5em;
    }

    .draggable-option >>> .question-toolbox{
        z-index: 1;
        position: relative;
        margin: -2px 0 0 0;
        background: #fff;
    }

    .draggable-option >>> .question-toolbox,
    .draggable-option >>> .question-toolbox .hidable{
        opacity:0;
    }

    .draggable-option:hover >>> .question-toolbox,
    .draggable-option:hover >>> .question-toolbox .hidable{
        opacity:1;
    }

    .draggable-option >>> .question-toolbox .question-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .draggable-option >>> .question-toolbox .question-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .draggable-option >>> .ivu-card-head{
        padding: 10px 10px 0 !important;
    }

    .draggable-option >>> .ivu-card-body{
        padding:0 !important;
    }

    .question-details{
        padding:16px;
    }

</style>

<template>

    <Card v-if="localQuestion" class="draggable-option mb-4">

        <Spin v-if="isDeletingQuestion" size="large" fix></Spin>

        <!-- Question Name -->
        <div slot="title" class="d-flex mb-2">

            <!-- Question Name Label  -->
            <span class="d-inline-block question-number font-weight-bold mr-2">
                {{ getQuestionNumber }}
            </span>
            <span class="d-inline-block question-text font-weight-bold">
                {{ localQuestion.text }}
            </span>
            
        </div>

        <!-- Question Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: -2px;">

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
                <Icon type="ios-move" class="question-icon dragger-handle hidable mr-2" size="20" />
            
            </div>

        </div>  

        <!-- Display Choices -->
        <ul v-if="localQuestion.choices.length" style="margin-left: 30px;" class="p-2">

            <li v-for="(choice, index) in localQuestion.choices">

                <span>{{ choice.text }}</span>

            </li>

        </ul>

        <!-- No questions message -->
        <Alert v-else type="info" class="m-2" show-icon>No choices</Alert>

        <div :class="(questionAndChoicesCharacters > 160 ? 'bg-warning ' : 'bg-grey-light ') + 'p-2 pl-4'">
            <span class="mr-2"><span class="font-weight-bold text-dark">Question:</span> {{ questionCharacters }}</span>
            <span class="mr-2"><span class="font-weight-bold text-dark">Choices:</span> {{ choicesCharacters }}</span>
            <span class="mr-2"><span class="font-weight-bold text-dark">Total:</span> {{ questionAndChoicesCharacters }}</span>
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
                isDeletingQuestion: false,
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
            },
            questionCharacters(){
                return this.localQuestion.text.length || 0;
            },
            choicesCharacters(){
                var total = 0;

                for(var x=0; x < this.localQuestion.choices.length; x++){
                    
                    total += this.localQuestion.choices[x].text.length;

                }
                
                return total;
            },
            questionAndChoicesCharacters(){
                return (this.questionCharacters + this.choicesCharacters)
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

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isDeletingQuestion = true;

                var question = this.topic.questions[index];
                
                api.call('delete', 'http://driving-theory.local/api/questions/'+question.id)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isDeletingQuestion = false;

                        //  Display Success Message
                        self.$Notice.success({
                            title: 'Question removed!'
                        });

                        //  Close Modal
                        self.topic.questions.splice(index, 1);

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isDeletingQuestion = false;

                        //  Log the responce
                        console.log(response);    
                    });
            }
        }
    }

</script>