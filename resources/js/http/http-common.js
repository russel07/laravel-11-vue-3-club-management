import axios from 'axios';
import requestHeader from "../helper/request-header";

export default axios.create({
    baseURL: process.env.APP_API_BASE_URL || 'http://lara-rest.test/api',
    headers:  requestHeader()
});
