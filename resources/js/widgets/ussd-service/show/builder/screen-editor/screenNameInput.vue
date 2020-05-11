<template>

    <!-- Screen Name Input -->
    <Input 
        type="text"
        class="w-100" :max="40"
        v-model="localScreen.name"
        placeholder="Enter the screen name"
        @on-blur="checkIfValidScreenName()"
        @on-focus="storeCurrentScreenName()">

        <span slot="prepend" class="font-weight-bold text-dark">Name</span>

    </Input>
    
</template>

<script>

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            builder: {
                type: Object,
                default: () => {}
            }
        },
        data(){
            return {
                localScreen: this.screen,
                screenNameBeforeChange: this.screen.name
            }
        }, 
        methods: {

            //  Get the screen name before any changes
            storeCurrentScreenName(){
                this.screenNameBeforeChange = this.localScreen.name;
            },

            //  Check if the given screen nams is valid
            checkIfValidScreenName(){

                //  Check if the given screen name is a duplicate name
                var duplicateName = (this.builder.screens.filter( (screen) => { 
                    return screen.name == this.localScreen.name;
                }).length >= 2) ? true : false;

                //  If the given screen name is a duplicate name
                if( duplicateName ){

                    this.$Notice.warning({
                        desc: 'Avoid using names of other screens'
                    });

                    this.localScreen.name = this.screenNameBeforeChange;

                }else if( this.localScreen.name == '' || this.localScreen.name == null ){

                    this.$Notice.warning({
                        desc: 'The screen name cannot be empty'
                    });

                    this.localScreen.name = this.screenNameBeforeChange;
                    
                }

            }
        }
    };
  
</script>