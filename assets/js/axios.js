import axios from "axios";
import {baseURL} from "./url";

const axiosInstance = axios.create({baseURL});

export default axiosInstance;
