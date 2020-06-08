import request from '../utils/request'

export default {
    install(Vue, options) {
        Vue.prototype.get = function(url, params) {
            return request({
                url: url,
                method: 'get',
                params: params
            })
        };

        Vue.prototype.post = function(url, data) {
            return request({
                url: url,
                method: 'post',
                data
            })
        }
    }
}
