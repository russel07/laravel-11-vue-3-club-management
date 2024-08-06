export default function requestHeader() {
    let token =  localStorage.getItem("_GymAppUserToken");

    if (token) {
        return {
            "Content-type": "application/json;charset=UTF-8",
            "Content-type": "multipart/form-data",
            "Access-Control-Allow-Origin": "*",
            'x-access-token': token,
            'Authorization' : 'Bearer '+token
        };
    } else {
        return {
            "Content-type": "application/json;charset=UTF-8",
            "Access-Control-Allow-Origin": "*",
            'content-type': 'multipart/form-data',
        };
    }
}
