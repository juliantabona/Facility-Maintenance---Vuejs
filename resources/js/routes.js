import VueRouter from 'vue-router';
import NProgress from 'nprogress';

let routes = [
    {
        //  Invoices
        path: '/invoices', name: 'invoices',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/invoice/list/main.vue')
    },
    {
        //  Create invoice
        path: '/invoices/create', name: 'create-invoice',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/invoice/create/main.vue'),
        props: true
    },
    {
        //  Show one invoice
        path: '/invoices/:id', name: 'show-invoice',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/invoice/show/main.vue'),
        props: true
    },
    {
        //  Show invoice activities
        path: '/invoices/:id/activities', name: 'show-invoice-activities',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/invoice/show/activities.vue'),
        props: true
    }

];

const router = new VueRouter({
    //mode: 'history',
    routes,
    
    /*
    *   Scroll to top on every navigation. Also handle edge cases such as:
    *   Saved Position - The saved position occurs when the user clicks the back or forward positions. 
    *                    We want to maintain the location the user was looking at. 
    *                    Visit: (https://router.vuejs.org/guide/advanced/scroll-behavior.html) 
    *   Hash Links - E.g. http://example.com/foo#bar should navigate to the element on the page with an id of bar.
    *                Visit: (https://router.vuejs.org/guide/advanced/scroll-behavior.html)
    *   Finally, in all other cases we can navigate to the top of the page.
    *   Here is the sample code that handles all of the above:
    */
    scrollBehavior: (to, from, savedPosition) => {
      if (savedPosition) {
        return savedPosition;
      } else if (to.hash) {
        return {
          selector: to.hash
        };
      } else {
        return { x: 0, y: 0 };
      }
    }
});

//  Then, we can apply a global beforeEach() method to our router, 
//  which lets us to perform any checks and actions before each route is loaded 
//  - just what middlewares in Laravel do.

router.beforeEach((to, from, next) => {
    console.log('Routes.js - Lets validate if user allowed to this route e.g) If route for Auth or Guest');
    console.log('From URL: '+ from.fullPath);
    console.log('To URL: '+ to.fullPath);

    //  Start page progress loader
    NProgress.start();

    //  Retrieve the matched route and check if it has meta.middlewareAuth set to true or set at all.
    //  If it's set to true it means we require the user to be authenticated to access the route and 
    //  if they're not we're redirecting them to the login page           
    if (to.matched.some(record => record.meta.middlewareAuth)) {     
        //  Check if user is authenticated
        if (!auth.check()) {
            console.log('Routes.js - Page only for Auth users ('+to.fullPath+')');
            //  Go to login page
            //  Save the route the user wanted too visit in the "redirect" query parameter
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            });

            return;
        }
    }

    //  Retrieve the matched route and check if it has meta.middlewareGuest set to true or set at all.
    //  If it's set to true it means the authenticated user cannot access the route and 
    //  if they are we're redirecting them to the dashboard overview page      
    if (to.matched.some(record => record.meta.middlewareGuest)) {     
        //  Check if user is authenticated
        if (auth.check()) {
            console.log('Routes.js - Page only for Guest users ('+to.fullPath+')');
            //  Go to Dashboard overview page
            next({
                path: '/dashboard'
            });

            return;
        }
    }

    next();
})

router.afterEach((to, from) => {
    //  Stop page progress loader
    NProgress.done()
})

export default router;