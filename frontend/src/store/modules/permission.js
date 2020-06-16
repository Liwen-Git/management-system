import {asyncRoutes, constantRoutes} from '@/router'

/**
 * Use meta.role to determine if the current user has permission
 * @param permissions
 * @param route
 */
function hasPermission(permissions, route) {
    if (route.children) {
        // 因为可能权限只勾选了子权限，父权限为半勾选，未写入数据库，所以只要有children就保留，然后再在deleteChildrenEmpty函数中处理children为空的情况
        return true;
    } else if (route.name) {
        return permissions.indexOf(route.name) >= 0;
    } else {
        return false;
    }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param permissions
 */
export function filterAsyncRoutes(routes, permissions) {
    const res = [];

    routes.forEach(route => {
        const tmp = {...route};
        if (hasPermission(permissions, tmp)) {
            if (tmp.children) {
                tmp.children = filterAsyncRoutes(tmp.children, permissions)
            }
            res.push(tmp)
        }
    });

    return res
}

// 动态路由 去除child为0的，保留alwaysShow为true的
function deleteChildrenEmpty(routesTmp) {
    const access = [];
    routesTmp.forEach((route) => {
        if (!route.children || route.alwaysShow) {
            // 没有子路由的都是有权限的，name在权限列表里面的
            access.push(route);
        } else if (route.children && route.children.length > 0) {
            // 递归循环子路由，子路由的children大于0则保留
            route.children = deleteChildrenEmpty(route.children);
            if (route.children.length > 0) {
                access.push(route);
            }
        }
    });

    return access;
}

const state = {
    routes: [],
    addRoutes: []
}

const mutations = {
    SET_ROUTES: (state, routes) => {
        state.addRoutes = routes
        state.routes = constantRoutes.concat(routes)
    }
}

const actions = {
    generateRoutes({commit}, {roles, permissions}) {
        return new Promise(resolve => {
            let accessedRoutes;
            if (roles.includes('super_admin')) {
                accessedRoutes = asyncRoutes || []
            } else {
                accessedRoutes = filterAsyncRoutes(asyncRoutes, permissions);

                accessedRoutes = deleteChildrenEmpty(accessedRoutes);
            }
            commit('SET_ROUTES', accessedRoutes);
            resolve(accessedRoutes)
        })
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
