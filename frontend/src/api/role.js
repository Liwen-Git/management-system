import request from '@/utils/request'

export function getRoutes() {
  return request({
    url: '/permission/list',
    method: 'get'
  })
}

export function getRoles() {
  return request({
    url: '/role/list',
    method: 'get'
  })
}

export function addRole(data) {
  return request({
    url: '/role/add',
    method: 'post',
    data
  })
}

export function updateRole(data) {
  return request({
    url: `/role/edit`,
    method: 'post',
    data
  })
}

export function deleteRole(data) {
  return request({
    url: `/role/delete`,
    method: 'post',
    data
  })
}
