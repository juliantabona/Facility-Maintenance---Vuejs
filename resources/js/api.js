class Api {
    constructor () {}

    /*  The call() method:
     *
     *  It just makes a regular AJAX call using axios, but adds some default logic to the catch method. 
     *  Now you can use this method for all your API calls instead of using axios directly. 
     *  Each AJAX call resulting in 401 will log the user out. You still can use the catch 
     *  method after call as you would with axios, the 401 condition won't be overridden.
     */

    call (requestType, url, data = null) {
        return new Promise((resolve, reject) => {
            axios[requestType](url, data)
                .then(response => {
                    resolve(response);
                })
                .catch(({response}) => {
                    if (response.status === 401) {
                        console.log('Api.js - User not authenticated, preparing to logout. url: '+url);
                        auth.logout();
                    }
    
                    reject(response);
                });
        });
    }

}

export default Api;