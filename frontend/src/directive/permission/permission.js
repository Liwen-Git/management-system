import store from '@/store'

export default {
    inserted(el, binding, vnode) {
        const {value} = binding
        const permissions = store.getters && store.getters.permissions;

        if (value) {
            const hasPermission = permissions.indexOf(value) >= 0;

            if (!hasPermission) {
                el.parentNode && el.parentNode.removeChild(el)
            }
        } else {
            throw new Error(`须填写权限指令! Like v-permission="xxx.add"`)
        }
    }
}
