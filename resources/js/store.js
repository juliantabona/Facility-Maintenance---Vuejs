class Store {
    constructor () {
        
        console.log('Store.js - Get the resourceType in local storage');

        /*  Get the resourceType stored in the local storage. 
         *  The resourceType value helps us understand whether to get information
         *  associated with the users company or branch. If we do not have any stored
         *  data, we will API to the database and verify on the users settings.
         */  
        this.resourceType = '';
        let data = window.localStorage.getItem('resourceType');

        //  If no data found
        if(!data){
            //  Api to get the users settings and update the resourseType
            data = this.getResourceType();
        }else{

            //  Otherwise get what we already have.
            this.resourceType = data;

        }
    
    }

    getResourceType() {

        console.log('Store.js - Get user settings to determine the resourceType');

        //  Make an Api call to get the authenticated user's settings
        api.call('get', '/api/user/settings')
            .then(({data}) => {
                console.log('Store.js - Got users settings');
                //  Get the resource type. Just incase we still can't determine.
                //  Default the resourseType to branch related information
                this.saveChanges(data);
            });
    }

    updateResourceType(updatedResourceType){  
        
        console.log('Store.js - save updated resource type - server side');

        //  Make an Api call to save the authenticated user's resourceType.
        //  This will allow other devices to pick up on the changes.
        var data = { resourceType: updatedResourceType };

        api.call('post', '/api/user/settings', data)
            .then(({data}) => {
                console.log('Store.js - Saved users settings');
                //  After saving the resource type. Just save changes locally as well
                this.saveChanges(data);
            })         
            .catch(error => {
                console.log('Store.js - Error saving resourceType...');
                console.log(error);
            });
    }

    saveChanges(data){
        console.log('Store.js - saved resourceType on server!');
        var resourceType = data.resourceType || 'branch';
        this.resourceType = resourceType;
        console.log(this.resourceType);
        window.localStorage.setItem('resourceType', this.resourceType);
    }

}

export default Store;