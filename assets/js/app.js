import Vue from "vue";
import axios from "./axios";
import url from "./url";

Vue.prototype.$axios = axios;
Vue.prototype.$url = url;

new Vue({
    el: "#app",

    data: {
    },
});
