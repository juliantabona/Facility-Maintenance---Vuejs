class Api {
    constructor () {}

    /*  The call() method:
     *
     *  It just makes a regular AJAX call using axios, but adds some default logic to the catch method. 
     *  Now you can use this method for all your API calls instead of using axios directly. 
     *  Each AJAX call resulting in 401 will log the user out. You still can use the catch 
     *  method after call as you would with axios, the 401 condition won't be overridden.
     */

    call (requestType, url, data = null, config = null) {
        return new Promise((resolve, reject) => {
            axios[requestType](url, data, config)
                .then(response => {
                    resolve(response);
                })
                .catch(({response}) => {
                    if (response.status === 401) {
                        console.log('Api.js - User not authenticated, preparing to logout. url: '+url);
                        auth.logout();
                    }

                    if( response.status == 403 ){

                        Event.$Notice.error({
                            title: 'UnAuthourized',
                            desc: response.data || 'You do not have permission to make this action.'
                        });

                    }

                    if (response.status >= 500) {
                        Event.$Notice.error({
                            title: 'Server Error - 500',
                            desc: response.data.message || 'Check your console for more information'
                        });
                      }
    
                    reject(response);
                });
        });
    }

}

export default Api;