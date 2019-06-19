import VueRouter from 'vue-router';
import NProgress from 'nprogress';

let routes = [
    //  Login
    {
        path: '/login', name: 'login',
        meta: { middlewareGuest: true },
        component: require('./views/auth/login.vue')
    },
    //  Register
    {
        path: '/register', name: 'register',
        meta: { middlewareGuest: true },
        component: require('./views/auth/register.vue')
    },
    //  Activate Account
    {
        path: '/activate-account', name: 'activate-account',
        meta: { middlewareGuest: true },
        component: require('./views/auth/ActivateAccount.vue')
    },
    //  Overview
    {
        path: '/overview', name: 'overview',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/overview/main.vue')
    },
        /**************************************
        *  INVOICES
        **************************************/  
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
    },
        /**************************************
        *  QUOTATIONS
        **************************************/  
    {
        //  Quotations
        path: '/quotations', name: 'quotations',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/quotation/list/main.vue')
    },
    {
        //  Create quotation
        path: '/quotations/create', name: 'create-quotation',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/quotation/create/main.vue'),
        props: true
    },
    {
        //  Show one quotation
        path: '/quotations/:id', name: 'show-quotation',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/quotation/show/main.vue'),
        props: true
    },
    {
        //  Show quotation activities
        path: '/quotations/:id/activities', name: 'show-quotation-activities',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/quotation/show/activities.vue'),
        props: true
    },
        /**************************************
        *  COMPANIES
        **************************************/  
       {
        //  companies
        path: '/companies', name: 'companies',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/company/list/main.vue')
    },
    {
        //  Create company
        path: '/companies/create', name: 'create-company',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/company/create/main.vue'),
        props: true
    },
    {
        //  Show one company
        path: '/companies/:id', name: 'show-company',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/company/show/main.vue'),
        props: true
    },
    {
        //  Show company activities
        path: '/companies/:id/activities', name: 'show-company-activities',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/company/show/activities.vue'),
        props: true
    },
    {
        //  Show company quotations
        path: '/companies/:id/quotations', name: 'show-company-quotations',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/company/show/quotations.vue'),
        props: true
    },
    {
        //  Show company invoices
        path: '/companies/:id/invoices', name: 'show-company-invoices',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/company/show/invoices.vue'),
        props: true
    },
    /**************************************
    *  USERS/INDIVIDUALS
    **************************************/  
    {
        //  Users
        path: '/users', name: 'users',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/user/list/main.vue')
    },
    {
        //  Create user
        path: '/users/create', name: 'create-user',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/user/create/main.vue'),
        props: true
    },
    {
        //  Show one user
        path: '/users/:id', name: 'show-user',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/user/show/main.vue'),
        props: true
    },
    {
        //  Show user activities
        path: '/users/:id/activities', name: 'show-user-activities',
        meta: { layout: 'Dashboard', middlewareAuth: true },
        component: require('./views/dashboard/user/show/activities.vue'),
        props: true
    },
    /**************************************
    *  JOBCARDS
    **************************************/  
   {
    //  jobcards
    path: '/jobcards', name: 'jobcards',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/list/main.vue')
},
{
    //  Create jobcard
    path: '/jobcards/create', name: 'create-jobcard',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/create/main.vue'),
    props: true
},
{
    //  Show jobcard calendar
    path: '/jobcards/calendar', name: 'jobcard-calendar',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/show/calendar.vue'),
    props: true
},
{
    //  Show one jobcard
    path: '/jobcards/:id', name: 'show-jobcard',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/show/main.vue'),
    props: true
},
{
    //  Show jobcard activities
    path: '/jobcards/:id/activities', name: 'show-jobcard-activities',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/show/activities.vue'),
    props: true
},
{
    //  Show jobcard quotations
    path: '/jobcards/:id/quotations', name: 'show-jobcard-quotations',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/show/quotations.vue'),
    props: true
},
{
    //  Show jobcard invoices
    path: '/jobcards/:id/invoices', name: 'show-jobcard-invoices',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/jobcard/show/invoices.vue'),
    props: true
},
    /**************************************
    *  APPOINTMENTS
    **************************************/  
{
    //  Appointments
    path: '/appointments', name: 'appointments',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/appointment/list/main.vue')
},
{
    //  Create appointment
    path: '/appointments/create', name: 'create-appointment',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/appointment/create/main.vue'),
    props: true
},,
{
    //  Show one appointment
    path: '/appointments/:id', name: 'show-appointment',
    meta: { layout: 'Dashboard', middlewareAuth: true },
    component: require('./views/dashboard/appointment/show/main.vue'),
    props: true
},
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
                path: '/overview'
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