import store from '@/store'

/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/directive.vue
 */
export default function checkPermission(value) {
    if (value) {
        const permissions = store.getters && store.getters.permissions;
        const roles = store.getters && store.getters.roles;

        const hasPermission = permissions.indexOf(value) >= 0;

        return hasPermission || roles.indexOf('super_admin') >= 0;
    } else {
        console.error(`须填写权限指令! Like v-permission="['xxx.add']"`);
        return false
    }
}
