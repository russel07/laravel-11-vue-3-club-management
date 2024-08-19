import axios from 'axios';
import requestHeader from "../helper/request-header";

export default axios.create({
    baseURL: process.env.APP_API_BASE_URL || 'https://demo.smarty-soft.com/api',
    headers:  requestHeader()
});
