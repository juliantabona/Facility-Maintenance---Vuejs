<template>
    <div>
        <!-- Modal -->
        <mainModal  
            :width="500"
            v-bind="$props"
            :isSaving="false" 
            okText="Create"
            cancelText="Cancel"
            :hideModal="hideModal"
            title="Create Question"
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Question -->
                <div class="d-flex mb-3">

                    <span class="text-dark font-weight-bold mr-1 mt-1">Question: </span>

                    <Input type="textarea" class="w-100 mb-2" v-model="question.text" placeholder="Write question..."></Input>
                
                </div>

                <div class="d-flex mb-3">

                    <span class="text-dark font-weight-bold mr-1 mt-1">Choices: </span>

                    <!-- Question Choices List & Dragger  -->
                    <draggable v-if="choicesExist"
                        :list="question.choices"
                        @start="drag=true" 
                        @end="drag=false" 
                        :options="{
                            group:'choices',
                            draggable:'.draggable-option', 
                            handle:'.dragger-handle'
                        }">

                        <!-- Single Choice  -->
                        <singleChoice v-for="(choice, index) in question.choices" :key="index"  
                            :question="question"
                            :choice="choice"
                            :index="index">
                        </singleChoice>

                    </draggable>

                    <!-- No choices message -->
                    <Alert v-else type="info" class="mb-2" show-icon>No Choices Found</Alert>
                
                </div>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    //  Main Modal
    import mainModal from '../../../../components/_common/modals/main.vue';

    export default {
        props:{
            topic: {
                type: Object,
                default:() => {}
            }
        },
        components: { mainModal },
        data(){
            return{
                hideModal: false,
                question: {
                    text: '',
                    choices: [],
                    topic_id: this.topic.id
                }
            }
        },
        computed: {

            //  Check if the choices exists
            choicesExist(){

                return (this.question.choices.length) ? true : false ;
            }

        },
        methods: {
            
        },
        created(){

        }
    }
</script>