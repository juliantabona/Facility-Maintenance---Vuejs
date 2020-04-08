<template>
  
    <Row :gutter="20">

        <Col :span="24">

            <div class="clearfix pb-2 mb-3 border-bottom">

                <span class="d-block mt-2 font-weight-bold text-dark float-left">Questions</span>

                <Button type="primary" class="p-1 float-right" @click.native="addQuestion()">
                    <Icon type="ios-add" :size="20" />
                    <span class="mr-2">Add Question</span>
                </Button>

            </div>

            <!-- Topic Question List & Dragger  -->
            <draggable v-if="questionsExist"
                :list="localTopic.questions"
                @start="drag=true" 
                @end="drag=false" 
                :options="{
                    group:'questions',
                    draggable:'.draggable-option', 
                    handle:'.dragger-handle'
                }">

                <!-- Single Question  -->
                <singleQuestion v-for="(question, index) in localTopic.questions" :key="index"  
                    :question="question"
                    :topic="localTopic"
                    :index="index">
                </singleQuestion>

            </draggable>

            <!-- No questions message -->
            <Alert v-else type="info" class="mb-2" show-icon>No Questions Found</Alert>

        </Col>

        <!-- 
            MODAL TO CREATE NEW EVENT
        -->
        <createQuestionModal 
            :topic="localTopic"
            v-if="isOpenCreateQuestionModal"
            @visibility="isOpenCreateQuestionModal = $event">
        </createQuestionModal>

    </Row>

</template>

<script>
    
    import draggable from 'vuedraggable';
    
    import singleQuestion from './singleQuestion.vue';
    
    import createQuestionModal from './create/createQuestionModal.vue';

    export default {
        props: { 
            topic:{
                type: Object,
                default: () => {}
            }
        },
        components: { 
            draggable, singleQuestion
        },
        data(){
            return {
                localTopic: this.topic
            }
        },
        watch: {
            //  Keep track of changes on the topic
            topic: {

                handler: function (val, oldVal) {
                    
                    this.localTopic = val;

                },
                deep: true

            },
        },
        computed: {

            //  Check if the questions exists
            questionsExist(){

                return (this.localTopic.questions.length) ? true : false ;
            }

        },
        methods: {
            addQuestion(){

                this.isOpenCreateQuestionModal = true;

            }
        }
    };
  
</script>