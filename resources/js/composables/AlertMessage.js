
import { ElNotification } from 'element-plus';

export default ()=> {
    const success = function(msg){
        ElNotification.success({
            title: 'Success',
            message: msg,
            offset: 100,
            duration: 2000,
            type: 'success',
        })
    }

    const warning = function(msg){
        ElNotification.warning({
            title: 'Warning',
            message: msg,
            offset: 100,
            duration: 2000,
        })
    }


    const info = function(msg){
        ElNotification.warning({
            title: 'Warning',
            message: msg,
            offset: 100,
            duration: 2000,
        })
    }


    const error = function(msg){
        ElNotification.warning({
            title: 'Warning',
            message: msg,
            offset: 100,
            duration: 2000,
        })
    }

    return {
        success,
        warning,
        info,
        error
    }
}
