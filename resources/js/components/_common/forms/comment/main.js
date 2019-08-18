
//  COMMENT FUNCTIONS
function getCommentFormFields(self) {
    return {
        text: null,
        rating: null,
        from_staff: self.sendAsStaff
    }
}

function getCommentFormRules(requiredTextError) {
    return {
        text: [
            { required: true, message: requiredTextError }
        ],
        rating: [
            { required: true, message: 'Select your rating' }
        ]
    }
}

function getCommentCustomErrorFields(self) {
    return getCommentFormFields(self);
}

function resetCommentCustomErrorFields(self) {
    self.commentCustomErrors = getCommentCustomErrorFields(self);
}

export default {
    getCommentFormFields,
    getCommentFormRules,
    getCommentCustomErrorFields,
    initiateComment(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['commentForm'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS SUBMIT THE COMMENT INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetCommentCustomErrorFields(self);

            //  Start loader
            self.isSavingComment = true;

            console.log('Attempt to comment using the following...');   

            //  Comment data to send
            let commentData = {
                text: self.commentForm.text,
                rating: self.commentForm.rating,
                from_staff: self.commentForm.from_staff
            };

            console.log(commentData);

            //  Use the api call() function located in resources/js/api.js
            return api.call('post', '/api/comments', commentData, self.urlParams)
                    .then(({data}) => {
                        
                        //  Reset the comment form fields
                        self.commentForm = getCommentFormFields(self);

                        //  Reset the comment custom errors
                        self.commentCustomErrors = getCommentCustomErrorFields(self);

                        //  Stop the loader
                        self.isSavingComment = false;

                        //  Reset form fields so that the form does not show errors
                        setTimeout(()=>{
                            self.$refs['commentForm'].resetFields();
                        },10);

                        return data;
                        
                    })         
                    .catch(response => { 
                        
                        console.log('components/_common/comment-user/main.js - Error loggin in...');
                        console.log(response);

                        //  Stop loader
                        self.isSavingComment = false;     
        
                    });

        }

        return false;

    }
}