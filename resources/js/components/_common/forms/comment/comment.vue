<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }
</style>
<template>

    <div>

        <!-- Comment Form-->
        <el-form :model="commentForm" :rules="commentFormRules" ref="commentForm">

            <!-- rating -->
            <el-form-item 
                v-if="canRate" prop="rating" :error="commentCustomErrors.rating">   
                    <span class="font-weight-bold d-block">YOUR RATING *</span>
                    <Rate :value="(commentForm.rating || 0)" class="tt-rating-stars" 
                            @on-change="commentForm.rating = $event" />
            </el-form-item>

            <!-- comment -->
            <el-form-item prop="text" :error="commentCustomErrors.text">
                <el-input :type="fieldType" v-model="commentForm.text" size="small" style="width:100%" :placeholder="placeholder"></el-input>
            </el-form-item>

            <!-- Loader -->
            <Loader v-if="isSavingComment" :loading="true" type="text" class="text-left mt-2">{{ loaderText }}</Loader>
            
            <div v-if="!isSavingComment" class="clearfix">

                <!-- Comment Button -->
                <basicButton
                    :customClass="btnClass" 
                    type="success" size="large" 
                    :ripple="(commentForm.rating && commentForm.text) ? true : false"
                    @click.native="handleComment()">
                    <span>{{ btnText }}</span>
                </basicButton>

            </div>

        </el-form>
        
    </div>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../loaders/Loader.vue'; 

    /*  Buttons  */
    import basicButton from './../../buttons/basicButton.vue';

    const commentHandle = require('./main.js').default;

    export default {
        components: { Loader, basicButton },
        props: {
            
            text: {
                type: String,
                default: null
            },
            /*  We use the sendAsStaff to identify whether the comment
                was sent by a staff member or a customer
            */
            sendAsStaff:{
                type: Boolean,
                default: false
            },
            /*  We use the urlParams to set more params on the api url
                when making the request to create the comment. We can 
                set params that decide which model id and model type
                we would like to save the comment to.
            */
            urlParams: {
                type: Object,
                default: null
            },

            requiredTextError: {
                type: String,
                default: 'Enter your comment'
            },

            btnText: {
                type: String,
                default: 'Post'
            },

            btnClass:{
                type: String,
                default: 'float-right mt-2 mb-2 ml-3 pl-3 pr-3'
            },
            
            placeholder: {
                type: String,
                default: 'Type your comment'
            },

            loaderText:{
                type: String,
                default: 'Commenting...'
            },

            fieldType: {
                type: String,
                default: 'textarea'
            },

            canRate: {
                type: Boolean,
                default: true
            }
        },
        data(){
            return {
                //  Comment Details
                commentForm: commentHandle.getCommentFormFields(this),
                commentFormRules: commentHandle.getCommentFormRules(this.requiredTextError),
                commentCustomErrors: commentHandle.getCommentCustomErrorFields(),
                isSavingComment: false,
            }
        },
        watch: {
            text: {
                handler: function (val, oldVal) {
                    this.commentForm.text = val;
                },
                deep: true
            },
            sendAsStaff: {
                handler: function (val, oldVal) {
                    this.commentForm.from_staff = val;
                },
                deep: true
            }
        },
        methods: {
            handleComment(){
                /*  Start the comment process by calling the function initiateComment() from
                    the commentHandle to handle the comment api request. We must pass "this" 
                    current vue instance in order to access data proprties of this comment
                    form. The initiateComment function will handle all validation of the 
                    comment form as well as return all the errors if any to the form fields.
                    If the comment if is a success we will return the data of the auth user
                    with token and user details. The token will already be set for 
                    future requests that require the auth token. We can use the then()
                    hook to determine what to do next if the comment is successful. In this
                    case we only want to alert the parent on the success of the comment.
                */
                var self = this;
                var commentResponse = commentHandle.initiateComment(this);
                
                //  If we have a comment response
                if(commentResponse){
                    
                    //  Hook into the response
                    commentResponse.then( data => {
                        
                        //  If not false
                        if( data !== false ){
                            //  If we have the comment data
                            //  Notify the parent and pass the comment data
                            self.$emit('commentSuccess', data);
                        }
                    });
                }

            }
        }
    }

</script>