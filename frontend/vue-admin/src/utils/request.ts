import axios from 'axios'
import { ElMessage } from 'element-plus'
import type { ApiResponse } from '@/types/api'
import router from '@/router'

// 创建axios实例
const request = axios.create({
  baseURL: '/adminapi',
  timeout: 10000
})

// 请求拦截器
request.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 响应拦截器
request.interceptors.response.use(
  (response) => {
    const res: ApiResponse = response.data
    
    if (res.code === 200) {
      return res.data
    } else {
      // 处理错误
      if (res.code === 401) {
        if (router.currentRoute.value.path !== '/login') {
          // 不在登录页面，清除token并显示提示
          localStorage.removeItem('token')
          ElMessage.error('登录已过期，请重新登录')
          router.push('/login')
        }else{
          return Promise.reject(new Error(res.msg || '登录失败'))
        }
        
      }
      
      ElMessage.error(res.msg || '请求失败')
      return Promise.reject(new Error(res.msg || '请求失败'))
    }
  },
  (error) => {
    ElMessage.error('网络错误或服务器异常')
    return Promise.reject(error)
  }
)

export default request