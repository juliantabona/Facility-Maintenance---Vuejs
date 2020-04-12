<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Choice Toolbox */

    .draggable-option {
        border:none;
    }

    .draggable-option >>> .choice-toolbox{
        z-index: 1;
        position: relative;
        margin: -2px 0 0 0;
        background: #fff;
    }

    .draggable-option >>> .choice-toolbox,
    .draggable-option >>> .choice-toolbox .hidable{
        opacity:0;
    }

    .draggable-option:hover >>> .choice-toolbox,
    .draggable-option:hover >>> .choice-toolbox .hidable{
        opacity:1;
    }

    .draggable-option >>> .choice-toolbox .choice-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .draggable-option >>> .choice-toolbox .choice-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .draggable-option >>> .ivu-card-body{
        padding:0px !important;
    }

</style>

<template>

    <Card v-if="localChoice" class="draggable-option mb-2">

        <!-- Choice Name -->
        <div slot="title">

            <Input :ref="'choice_'+index" type="textarea" class="choice-textarea w-100" v-model="localChoice.text" placeholder="Write choice..."></Input>

            <div class="d-flex mt-2">

                <RadioGroup :value="isCorrect" name="is_correct" @on-change="handleIsCorrect($event)">
                    <Radio label="Correct"></Radio>
                    <Radio label="Wrong"></Radio>
                </RadioGroup>

            </div>
    
        </div>

        <!-- Choice Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: -2px;">

            <div class="choice-toolbox">

                <!-- Remove Choice Button  -->
                <Poptip confirm title="Are you sure you want to remove this choice?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveChoice(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="choice-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Move Choice Button  -->
                <Icon type="ios-move" class="choice-icon dragger-handle hidable mr-2" size="20" />
            
            </div>

        </div>   
    
    </Card>
                
</template>

<script>

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            choice: {
                type: Object,
                default:() => {}
            },
            question: {
                type: Object,
                default:() => {}
            }
        }, 
        data(){
            return {
                localChoice: this.choice
            }
        },
        watch: {
            //  Keep track of changes on the choice
            choice: {

                handler: function (val, oldVal) {
                    
                    this.localChoice = val;

                },
                deep: true

            },
        },
        computed: {
            isCorrect(){
                return this.localChoice.is_correct ? 'Correct' : 'Wrong';
            }
        },
        methods: {
            handleIsCorrect(is_correct){
                console.log('is_correct:' +is_correct);
                this.localChoice.is_correct = (is_correct == 'Correct' ? true : false);

            },
            editChoice(){
                this.isOpenEditChoiceModal = true;
            },
            handleDuplicateChoice(index) {

                //  Duplicate the choice
                var duplicateChoice = _.cloneDeep( this.question.choices[index] );

                //  Create the duplicate choice name
                var duplicateName = 'Choice - #' + (this.question.choices.length + 1);

                //  Set the duplicate choice name
                duplicateChoice.name = duplicateName;

                //  Add the duplicate choice to the rest of the other choices
                this.question.choices.push(duplicateChoice);

            },
            handleRemoveChoice(index) {
                this.question.choices.splice(index, 1);
            }
        }
    }

</script>