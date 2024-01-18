import axios from 'axios';
import toast from '../plugins/toast'

const errorComposer = (err) => {
    return () => {
        let message = err?.response?.data?.message ?? "";
        let errors = err?.response?.data?.errors ?? {};

        if (errors && Object.keys(errors).length > 0) {
            //validation errors
            let errorMsg = `<ul>`
            Object.keys(errors).forEach((key, index) => {
                errorMsg += `<li>${errors[key][0]}</li>`
            })
            errorMsg += `</ul>`
            toast.show_toast('error', errorMsg);
        } else if (message) {
            //normal msg
            toast.show_toast('error', message);
        } else {
            //req. failed msg
            toast.show_toast('error', err.toString())
        }
    }
}


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = window.location.origin + "/api";
// errorComposer will compose a handleGlobally function
axios.interceptors.response.use(undefined, function (error) {
    error.handleGlobally = errorComposer(error);
    return Promise.reject(error);
})
window.axios = axios;