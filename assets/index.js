import "./css/app.less";

import { createApp } from "vue";
import axios from "./js/axios";
import url from "./js/url";

const app = createApp({
    data() {
        return {};
    },
});

app.provide("$axios", axios);
app.provide("$url", url);
app.mount("#app");
