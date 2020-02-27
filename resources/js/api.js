class Api {
    constructor () {}

    /*  The call() method:
     *
     *  It just makes a regular AJAX call using axios, but adds some default logic to the catch method. 
     *  Now you can use this method for all your API calls instead of using axios directly. 
     *  Each AJAX call resulting in 401 will log the user out. You still can use the catch 
     *  method after call as you would with axios, the 401 condition won't be overridden.
     */

    call (requestType, url, data = null, urlParams = null, config = null) {
        return new Promise((resolve, reject) => {
            
            var query = '';

            if(urlParams){
                //  Get the urlParams and format for the api call
                for(var x=0; x < Object.keys(urlParams).length; x++){
                    //  Get the current query key
                    let key = Object.keys(urlParams)[x];
                    //  Get the current query value
                    let value = Object.values(urlParams)[x];
                    
                    //  If this is the first query
                    if(x == 0){
                        query = '?'+key +'='+value;
                    }else{
                        query = query +'&'+ (key +'='+value);
                    }
                }
                //  Update the data with the query parameters if any
                url = url + query;
            }

            axios[requestType](url, data, config)
                .then(response => {
                    resolve(response);
                })
                .catch(response => {

                    console.log('Error 1 Response!!!!');
                    console.log(response);
                    
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