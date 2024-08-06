export default function requestHeader(isMultipart = false) {
    let token = localStorage.getItem("_GymAppUserToken");

    let headers = {
        'Authorization': token ? `Bearer ${token}` : '',
    };

    if (isMultipart) {
        headers['Content-Type'] = 'multipart/form-data';
    } else {
        headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    return headers;
}

