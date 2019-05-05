class Store {
    constructor () {
        
        console.log('Store.js - Get the allocationType in local storage');

        /*  Get the allocationType stored in the local storage. 
         *  The allocationType value helps us understand whether to get information
         *  associated with the users company or branch. If we do not have any stored
         *  data, we will API to the database and verify on the users settings.
         */  
        this.allocationType = '';

        let data = window.localStorage.getItem('allocationType');

        //  If no data found
        if(!data){
            //  Check if user is authenticated
            if (auth.check()) {
                //  Api to get the users settings and update the allocationType
                data = this.getAllocationType();
            }
        }else{
            
            //  Otherwise get what we already have.
            this.allocationType = data;

        }
    
    }

    getAllocationType() {

        console.log('Store.js - Get user settings to determine the allocationType');

        //  Make an Api call to get the authenticated user's settings
        api.call('get', '/api/user/settings')
            .then(({data}) => {
                console.log('Store.js - Got users settings');
                //  Get the allocation type. Just incase we still can't determine.
                //  Default the allocationType to branch related information
                this.saveChanges(data);
            });
    }

    updateAllocationType(updatedallocationType){  
        
        console.log('Store.js - save updated allocation type - server side');

        //  Make an Api call to save the authenticated user's allocationType.
        //  This will allow other devices to pick up on the changes.
        var data = { allocationType: updatedallocationType };

        api.call('post', '/api/user/settings', data)
            .then(({data}) => {
                console.log('Store.js - Saved users settings');
                //  After saving the allocation type. Just save changes locally as well
                this.saveChanges(data);
            })         
            .catch(error => {
                console.log('Store.js - Error saving allocation type...');
                console.log(error);
            });
    }

    saveChanges(data){
        console.log('Store.js - saved allocation type on server!');
        var allocationType = data.allocationType || 'branch';
        this.allocationType = allocationType;
        console.log(this.allocationType);
        window.localStorage.setItem('allocationType', this.allocationType);
    }

}

export default Store;