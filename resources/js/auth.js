import router from './routes.js';

class Auth {
    constructor () {
        
        console.log('Auth.js - Get the token and user in local storage');

        //  Get the token stored in the local storage
        this.token = window.localStorage.getItem('token');

        //  Get the user stored in the local storage 
        let userData = window.localStorage.getItem('user');
        this.user = userData ? JSON.parse(userData) : null;
    
        //  If the token exists 
        if (this.token) {
            //  Set the token to axios header for authentication use in future Api calls
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            
            //  Verfiy and updated the authenticated user
            this.getUser();
        }
    }

    getUser() {

        console.log('Auth.js - Verfiy and update the authenticated user');

        //  Make an Api call to get the authenticated user with the assigned auth token
        return api.call('get', '/api/me')
            .then(({data}) => {

                //  Update the user details
                this.user = data;
                window.localStorage.setItem('user', JSON.stringify(data));
                
            });
    }

    login (token, user) {   

        console.log('Auth.js - save token and user');   

        window.localStorage.setItem('token', token);
        window.localStorage.setItem('user', JSON.stringify(user));
    
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
    
        this.token = token;
        this.user = user;
    
        Event.$emit('userLoggedIn');
    }

    logout(){  
        
        console.log('auth.js - start loggin out - server side');

        //  Use the api call() function located in resources/js/api.js
        axios.post('/api/logout')
            .then(({data}) => {

                console.log('auth.js - continue loggin out - client side');

                this.clearLocalData();

                console.log('auth.js - Go to login page');
            })         
            .catch(error => {
                console.log('auth.js - Error loggin out...');
                console.log(error);
                //  If not authenticated to logout server side, then logout only client side
                if( ((error || {}).response || {}).status == 401){
                    this.clearLocalData();
                }
            });
    }

    clearLocalData(){
        console.log('auth.js - Remove local token and user');
        localStorage.removeItem('token');
        localStorage.removeItem('user');

        this.token = null;
        this.user = null;

        router.push({ name: 'login' })

    }

    check () {
        console.log('Auth.js - Check if authenticated');   
        return !! this.token;
    }

}

export default Auth;