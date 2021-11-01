import Vue from "vue";
window.Vue = require("vue").default;
require("./bootstrap");

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component("app-layout", require("./components/AppLayout.vue").default);
Vue.component(
    "form-component",
    require("./components/FormComponent.vue").default
);
Vue.component("base-table", require("./components/BaseTable.vue").default);

const app = new Vue({
    el: "#app",
});
