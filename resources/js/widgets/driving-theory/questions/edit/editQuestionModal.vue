<template>

    <div>

        <!-- Modal -->
        <mainModal  
            :width="500"
            v-bind="$props"
            okText="Create"
            cancelText="Cancel"
            title="Edit Question"
            :hideModal="hideModal"
            @on-ok="saveChanges()"
            :isSaving="isSavingChanges" 
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
            question: {
                type: Object,
                default:() => {}
            }
        },
        components: { mainModal },
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

                return (this.question.choices.length) ? true : false ;
            }

        },
        methods: {
            
            saveChanges() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isSavingChanges = true;
                
                api.call('post', 'http://127.0.0.1:8000/questions/'+this.question.id)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isSavingChanges = false;

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