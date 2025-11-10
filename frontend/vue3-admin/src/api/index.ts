import request from '@/utils/request'

// 用户相关API
export const authAPI = {
  /**
   * 用户登录
   */
  login: (username: string, password: string) => {
    return request.post<{
      token: string
      user_info: {
        id: string
        username: string
        nickname: string
        avatar: string
        role: string
      }
    }>('/auth/login', { username, password })
  },

  /**
   * 用户退出
   */
  logout: () => {
    return request.post('/auth/logout')
  },

  /**
   * 刷新token
   */
  refreshToken: () => {
    return request.post<{ token: string }>('/auth/refresh')
  },

  /**
   * 获取用户信息
   */
  getUserInfo: () => {
    return request.get<{
      id: string
      username: string
      nickname: string
      avatar: string
      role: string
      permissions: string[]
    }>('/auth/user')
  }
}

// 用户管理API
export const userAPI = {
  /**
   * 获取用户列表
   */
  getList: (params: {
    page?: number
    limit?: number
    username?: string
    role?: string
    status?: number
  }) => {
    return request.get<{
      list: Array<{
        id: string
        username: string
        nickname: string
        avatar: string
        role: string
        status: number
        last_login_time: string
        create_time: string
      }>
      pagination: {
        total: number
        page: number
        limit: number
        pages: number
      }
    }>('/user/list', params)
  },

  /**
   * 创建用户
   */
  create: (data: {
    username: string
    password: string
    nickname?: string
    role?: string
    status?: number
  }) => {
    return request.post('/user/create', data)
  },

  /**
   * 更新用户
   */
  update: (id: string, data: {
    nickname?: string
    role?: string
    status?: number
    password?: string
  }) => {
    return request.put(`/user/update/${id}`, data)
  },

  /**
   * 删除用户
   */
  delete: (id: string) => {
    return request.delete(`/user/delete/${id}`)
  },

  /**
   * 获取用户详情
   */
  detail: (id: string) => {
    return request.get<{
      id: string
      username: string
      nickname: string
      avatar: string
      role: string
      status: number
      last_login_time: string
      create_time: string
    }>(`/user/detail/${id}`)
  }
}

// 系统管理API
export const systemAPI = {
  /**
   * 获取系统配置
   */
  getConfig: () => {
    return request.get<Record<string, any>>('/system/config')
  },

  /**
   * 更新系统配置
   */
  updateConfig: (data: Record<string, any>) => {
    return request.post('/system/config/update', data)
  }
}

// 文件上传API
export const uploadAPI = {
  /**
   * 上传文件
   */
  upload: (file: File) => {
    return request.upload<{
      url: string
      filename: string
      size: number
    }>('/upload/file', file)
  },

  /**
   * 上传图片
   */
  uploadImage: (file: File) => {
    return request.upload<{
      url: string
      filename: string
      size: number
      width: number
      height: number
    }>('/upload/image', file)
  }
}

export default {
  auth: authAPI,
  user: userAPI,
  system: systemAPI,
  upload: uploadAPI
}