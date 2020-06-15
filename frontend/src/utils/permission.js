import store from '@/store'

/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/directive.vue
 */
export default function checkPermission(value) {
    if (value) {
        const permissions = store.getters && store.getters.permissions;

        return permissions.indexOf(value) >= 0;
    } else {
        console.error(`须填写权限指令! Like v-permission="['xxx.add']"`);
        return false
    }
}
