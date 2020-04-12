<template>

    <div>

        <!-- Modal -->
        <mainModal  
            :width="600"
            v-bind="$props"
            cancelText="Cancel"
            okText="Save Changes"
            title="Edit Question"
            :maskClosable="false"
            :hideModal="hideModal"
            @on-ok="saveChanges()"
            :isSaving="isSavingChanges" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Question -->
                <div class="mb-3">

                    <span class="text-dark font-weight-bold d-block mr-1 mt-1 mb-1">Question: </span>

                    <Input type="textarea" class="w-100" v-model="localQuestion.text" placeholder="Write question..."></Input>
                
                </div>

                <div class="mb-2">

                    <div class="clearfix">
                        <span class="float-left text-dark font-weight-bold d-block border-bottom pb-2 mr-1 mt-1 mb-1 ">Choices: </span>
                        
                        <Button type="default" class="p-1 float-right" @click.native="addChoice()">
                            <Icon type="ios-refresh" :size="20" />
                            <span class="mr-2">Add Choice</span>
                        </Button>
                    </div>

                    <!-- Question Choices List & Dragger  -->
                    <draggable v-if="choicesExist"
                        :list="localQuestion.choices"
                        @start="drag=true" 
                        @end="drag=false" 
                        :options="{
                            group:'choices',
                            draggable:'.draggable-option', 
                            handle:'.dragger-handle'
                        }">

                        <!-- Single Choice  -->
                        <singleChoice v-for="(choice, index) in localQuestion.choices" :key="index"  
                            :question="localQuestion"
                            :choice="choice"
                            :index="index">
                        </singleChoice>

                    </draggable>

                    <!-- No choices message -->
                    <Alert v-else type="info" class="mt-2 mb-2" show-icon>No choices found</Alert>
                
                </div>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    import draggable from 'vuedraggable';
    
    import singleChoice from './../singleChoice.vue';

    //  Main Modal
    import mainModal from '../../../../components/_common/modals/main.vue';

    export default {
        props:{
            question: {
                type: Object,
                default:() => {}
            }
        },
        components: { draggable, singleChoice, mainModal },
        data(){
            return{
                hideModal: false,
                isSavingChanges: false,
                localQuestion: this.question
            }
        },
        computed: {

            //  Check if the choices exists
            choicesExist(){

                return (this.localQuestion.choices.length) ? true : false ;
            }

        },
        methods: {
            closeModal(){
                this.hideModal = true;
            },
            addChoice(){

                var numberOfChoices = this.localQuestion.choices.length;

                this.localQuestion.choices.push({
                    text: '',
                    is_correct: true
                });
            },
            saveChanges() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isSavingChanges = true;

                var formData = this.localQuestion;
                
                api.call('put', 'http://driving-theory.local/api/questions/'+this.localQuestion.id, formData)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isSavingChanges = false;

                        //  Display Success Message
                        self.$Notice.success({
                            title: 'Changes saved!'
                        });

                        //  Close Modal
                        self.closeModal();

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isSavingChanges = false;

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){

        }
    }
</script>