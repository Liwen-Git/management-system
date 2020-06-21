import request from '../utils/request'

export default {
    install(Vue, options) {
        Vue.prototype.axiosGet = function(url, params) {
            return request({
                url: url,
                method: 'get',
                params: params
            })
        };

        Vue.prototype.axiosPost = function(url, data) {
            return request({
                url: url,
                method: 'post',
                data
            })
        }
    }
}
