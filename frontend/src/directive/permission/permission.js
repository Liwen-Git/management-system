import store from '@/store'

export default {
    inserted(el, binding, vnode) {
        const {value} = binding
        const permissions = store.getters && store.getters.permissions;
        const roles = store.getters && store.getters.roles;

        if (value) {
            const hasPermission = permissions.indexOf(value) >= 0;

            if (!hasPermission && roles.indexOf('super_admin') < 0) {
                el.parentNode && el.parentNode.removeChild(el)
            }
        } else {
            throw new Error(`须填写权限指令! Like v-permission="xxx.add"`)
        }
    }
}
