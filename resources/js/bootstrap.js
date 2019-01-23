
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
     broadcaster: 'pusher',
     key: '0afa69f43d05b964054e',
     cluster: 'ap2',
     encrypted: true,
     /*
      * I have added a custom authentication route to check if the user
      * is authourized to listen to any channel that requires authentication. 
      * The reason why i created a custom route for this was because of
      * errors i got due to the default authentication done by "Broadcast::routes();"
      * located in the "BroadcastServiceProvider". I would fail with the error
      * "419 - Sorry, your session has expired. Please refresh and try again." 
      * because i added "\Illuminate\Session\Middleware\StartSession::class," 
      * to "api" middleware in "App/Http/Kernel.php".
      */
     authEndpoint: 'api/pusher/auth'
});
