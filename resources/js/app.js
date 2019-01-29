
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

//  Layout Components
Vue.component('oq-Basic-Layout',  require('./layouts/main/basic-layout.vue'));
Vue.component('oq-Dashboard-Layout',  require('./layouts/main/dashboard-layout.vue'));
Vue.component('oq-Header',  require('./layouts/header/default.vue'));
Vue.component('oq-Aside',  require('./layouts/aside/default.vue'));

//  Draggable Components

Vue.component('oq-Template-Content',  require('./views/dashboard/draggable/template/content/main.vue'));
Vue.component('oq-Template-Sidebar',  require('./views/dashboard/draggable/template/sidebar/main.vue'));
Vue.component('oq-Template-Header',  require('./views/dashboard/draggable/template/header/main.vue'));
Vue.component('oq-Template-Sections',  require('./views/dashboard/draggable/sections/main.vue'));
Vue.component('oq-Template-Section',  require('./views/dashboard/draggable/sections/single.vue'));
Vue.component('oq-Template-Field',  require('./views/dashboard/draggable/fields/single.vue'));
Vue.component('oq-Template-Field-Mockup',  require('./views/dashboard/draggable/fields/mockups.vue'));
//Vue.component('oq-Template-Drawer',  require('./views/dashboard/draggable/drawer/main.vue'));
//Vue.component('oq-Template-Modal',  require('./views/dashboard/draggable/modal/main.vue'));

Vue.component('create-draggable-section',  require('./views/dashboard/draggable/create-section-modal.vue'));
Vue.component('show-draggable-section',  require('./views/dashboard/draggable/show-section-modal.vue'));
Vue.component('create-draggable-field',  require('./views/dashboard/draggable/create-field-modal.vue'));
Vue.component('field-template',  require('./views/dashboard/draggable/field-template.vue'));
Vue.component('section-edit-drawer',  require('./views/dashboard/draggable/section-edit-drawer.vue'));
Vue.component('field-edit-drawer',  require('./views/dashboard/draggable/field-edit-drawer.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Vue from 'vue';
import App from './App.vue';

//  Import NProgress for showing loading progress bar
import NProgress from 'nprogress';
import 'nprogress/nprogress.css'

//  Import Vue Router for custom routes and navigation
import VueRouter from 'vue-router';
import router from './routes.js';

Vue.use(VueRouter)

//  Import Api For Api handling [A custom js file we created]
import Api from './api.js';

window.api = new Api();

//  Import Auth For authentication handling [A custom js file we created]
import Auth from './auth.js';

window.auth = new Auth();

//  Import Store For storage handling of global variables [A custom js file we created]
import Store from './store.js';

window.store = new Store();

//  Global event manager, to emit changes/updates
//  such as when user has logged in e.g) auth.js
window.Event = new Vue;


//  Import & Use iView UI Toolkit
import iView from 'iview';
import 'iview/dist/styles/iview.css';
import iViewlocale from 'iview/dist/locale/en-US';

Vue.use(iView, {locale: iViewlocale});

//  Import & Use Element UI Toolkit
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en';

Vue.use(ElementUI, { locale });

//  Imports Moment.js for use by vue components in formatting dates
Vue.use(require('vue-moment'));

//  Imports Vue2Filters for use by vue components e.g) capitalize, truncate, format money, e.t.c
import Vue2Filters from 'vue2-filters';

Vue.use(Vue2Filters);

var VueScrollTo = require('vue-scrollto');

Vue.use(VueScrollTo)

// Imports Froala Editor for wysiwyg functionality
require('froala-editor/js/froala_editor.pkgd.min')

// Require Froala Editor css files.
require('froala-editor/css/froala_editor.pkgd.min.css')
require('font-awesome/css/font-awesome.css')
require('froala-editor/css/froala_style.min.css')

// Import and use Vue Froala lib.
import VueFroala from 'vue-froala-wysiwyg'

Vue.use(VueFroala)

const app = new Vue({
    el: '#app',
    //  Render the main app view
    render: h => h(App),
    //  For our custom routes
    router
  });
