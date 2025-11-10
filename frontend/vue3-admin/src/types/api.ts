// 基础响应类型
export interface BaseResponse<T = any> {
  code: number
  message: string
  data: T
  timestamp: number
}

// 分页响应类型
export interface PaginatedResponse<T = any> {
  list: T[]
  pagination: {
    total: number
    page: number
    limit: number
    pages: number
  }
}

// 用户相关类型
export interface UserInfo {
  id: string
  username: string
  nickname: string
  avatar: string
  role: string
  status?: number
  last_login_time?: string
  create_time?: string
}

export interface LoginParams {
  username: string
  password: string
}

export interface LoginResponse {
  token: string
  user_info: UserInfo
}

export interface RefreshTokenResponse {
  token: string
}

// 用户管理相关类型
export interface UserListParams {
  page?: number
  limit?: number
  username?: string
  role?: string
  status?: number
}

export interface CreateUserParams {
  username: string
  password: string
  nickname?: string
  role?: string
  status?: number
}

export interface UpdateUserParams {
  nickname?: string
  role?: string
  status?: number
  password?: string
}

// 文件上传相关类型
export interface UploadResponse {
  url: string
  filename: string
  size: number
  width?: number
  height?: number
}