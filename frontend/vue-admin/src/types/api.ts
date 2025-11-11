// API响应类型
export interface ApiResponse<T = any> {
  code: number
  msg: string
  data: T
  timestamp: number
}

// 用户相关类型
export interface UserInfo {
  user_id: number
  username: string
  nickname: string
  avatar: string
  email: string
  phone: string
  dept_id: number
  sex: number
  status: number
  created_at: string
}

export interface LoginParams {
  username: string
  password: string
}

export interface LoginResponse {
  token: string
  user: UserInfo
}