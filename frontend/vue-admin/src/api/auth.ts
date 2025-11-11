import request from '@/utils/request'
import type { LoginParams, LoginResponse, UserInfo } from '@/types/api'

// 登录接口
export const login = (data: LoginParams) => {
  return request.post<LoginResponse>('/auth/login', data)
}

// 获取用户信息
export const getUserInfo = () => {
  return request.get<UserInfo>('/auth/user')
}

// 退出登录
export const logout = () => {
  return request.post('/auth/logout')
}