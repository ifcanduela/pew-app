import axios from "axios";

const m = document.querySelector("meta[name=base-url]");
const baseURL = m ? m.content : "/";
const axiosInstance = axios.create({baseURL});

export default axiosInstance;
