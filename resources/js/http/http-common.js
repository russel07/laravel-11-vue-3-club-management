import axios from 'axios';
import requestHeader from "../helper/request-header";

export default axios.create({
    baseURL: '/api',
    headers:  requestHeader()
});

