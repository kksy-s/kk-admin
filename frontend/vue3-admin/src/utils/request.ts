import { ElMessage, ElMessageBox } from 'element-plus'
import { useUserStore } from '@/stores/user'

// 基础配置 - 直接使用本地配置
const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/admin'
const TIMEOUT = 10000 // 10秒超时

// 响应数据类型定义
interface ResponseData<T = any> {
  code: number
  message: string
  data: T
  timestamp: number
}

// 请求配置类型
interface RequestConfig {
  url: string
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH'
  data?: any
  params?: any
  headers?: Record<string, string>
  timeout?: number
  showLoading?: boolean
  showError?: boolean
  responseType?: 'json' | 'text' | 'blob' | 'arraybuffer'
}

class Request {
  private baseURL: string
  private timeout: number

  constructor(baseURL: string = BASE_URL, timeout: number = TIMEOUT) {
    this.baseURL = baseURL
    this.timeout = timeout
  }

  /**
   * 发送请求
   */
  async request<T = any>(config: RequestConfig): Promise<T> {
    const {
      url,
      method = 'GET',
      data,
      params,
      headers = {},
      timeout = this.timeout,
      showLoading = true,
      showError = true,
      responseType = 'json'
    } = config

    // 构建完整URL - 使用相对路径
    let fullURL = this.baseURL + url
    
    // 处理查询参数
    if (params && Object.keys(params).length > 0) {
      const queryString = new URLSearchParams(params).toString()
      fullURL += (fullURL.includes('?') ? '&' : '?') + queryString
    }

    // 设置请求头
    const defaultHeaders: Record<string, string> = {
      'Content-Type': 'application/json'
    }

    // 添加认证token - 排除登录接口
    const userStore = useUserStore()
    if (userStore.token && !url.includes('/auth/login')) {
      defaultHeaders['Authorization'] = `Bearer ${userStore.token}`
    }

    // 合并请求头
    const finalHeaders = { ...defaultHeaders, ...headers }

    // 显示加载提示
    let loadingInstance: any = null
    if (showLoading) {
      loadingInstance = ElMessage({
        message: '加载中...',
        type: 'info',
        duration: 0,
        showClose: true
      })
    }

    try {
      const controller = new AbortController()
      const timeoutId = setTimeout(() => controller.abort(), timeout)

      const response = await fetch(fullURL, {
        method,
        headers: finalHeaders,
        body: method !== 'GET' ? JSON.stringify(data) : undefined,
        signal: controller.signal
      })

      clearTimeout(timeoutId)

      // 处理响应
      let responseData: any
      if (responseType === 'json') {
        responseData = await response.json()
      } else if (responseType === 'text') {
        responseData = await response.text()
      } else if (responseType === 'blob') {
        responseData = await response.blob()
      } else if (responseType === 'arraybuffer') {
        responseData = await response.arrayBuffer()
      }

      // 检查HTTP状态码
      if (!response.ok) {
        // 处理认证错误（401）- 对应后台的"用户名或密码错误"、"用户不存在"
        if (response.status === 401) {
          // 尝试从响应中获取具体的错误信息
          let errorMessage = '登录已过期，请重新登录'
          if (responseData && responseData.message) {
            errorMessage = responseData.message
          }
          throw new Error(errorMessage)
        }
        
        // 处理权限错误（403）- 对应后台的"账号已被禁用"
        if (response.status === 403) {
          let errorMessage = '无权限访问此资源'
          if (responseData && responseData.message) {
            errorMessage = responseData.message
          }
          throw new Error(errorMessage)
        }
        
        // 处理其他HTTP错误
        let errorMessage = `请求失败，状态码: ${response.status}`
        if (responseData && responseData.message) {
          errorMessage = responseData.message
        }
        
        throw new Error(errorMessage)
      }

      // 检查业务状态码（如果响应是JSON格式且有业务状态码）
      if (responseType === 'json' && responseData && responseData.code !== undefined) {
        if (responseData.code !== 200) {
          // Token过期特殊处理
          if (responseData.code === 401) {
            await this.handleUnauthorized()
          }

          // 直接显示后端返回的错误消息
          throw new Error(responseData.message || '请求失败')
        }
        
        return responseData.data
      }

      // 如果没有业务状态码，直接返回响应数据
      return responseData

    } catch (error: any) {
      if (showError) {
        if (error.name === 'AbortError') {
          ElMessage.error('请求超时，请稍后重试')
        } else if (error.message.includes('Failed to fetch')) {
          ElMessage.error('网络连接失败，请检查网络设置')
        } else {
          // 直接显示错误消息（后端返回什么就显示什么）
          ElMessage.error(error.message || '请求失败，请稍后重试')
        }
      }

      throw error
    } finally {
      // 关闭加载提示
      if (loadingInstance) {
        loadingInstance.close()
      }
    }
  }

  /**
   * 处理未授权情况
   */
  private async handleUnauthorized() {
    const userStore = useUserStore()
    
    try {
      // 尝试刷新token
      const result = await userStore.refreshToken()
      if (!result.success) {
        // 刷新失败，跳转到登录页
        await ElMessageBox.alert('登录已过期，请重新登录', '提示', {
          confirmButtonText: '确定',
          callback: () => {
            userStore.logout()
            window.location.href = '/login'
          }
        })
      }
    } catch (error) {
      // 刷新失败，跳转到登录页
      await ElMessageBox.alert('登录已过期，请重新登录', '提示', {
        confirmButtonText: '确定',
        callback: () => {
          userStore.logout()
          window.location.href = '/login'
        }
      })
    }
  }

  /**
   * GET请求
   */
  get<T = any>(url: string, params?: any, config?: Omit<RequestConfig, 'url' | 'method' | 'params'>) {
    return this.request<T>({
      url,
      method: 'GET',
      params,
      ...config
    })
  }

  /**
   * POST请求
   */
  post<T = any>(url: string, data?: any, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>) {
    return this.request<T>({
      url,
      method: 'POST',
      data,
      ...config
    })
  }

  /**
   * PUT请求
   */
  put<T = any>(url: string, data?: any, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>) {
    return this.request<T>({
      url,
      method: 'PUT',
      data,
      ...config
    })
  }

  /**
   * DELETE请求
   */
  delete<T = any>(url: string, params?: any, config?: Omit<RequestConfig, 'url' | 'method' | 'params'>) {
    return this.request<T>({
      url,
      method: 'DELETE',
      params,
      ...config
    })
  }

  /**
   * PATCH请求
   */
  patch<T = any>(url: string, data?: any, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>) {
    return this.request<T>({
      url,
      method: 'PATCH',
      data,
      ...config
    })
  }

  /**
   * 上传文件
   */
  async upload<T = any>(url: string, file: File, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>) {
    const formData = new FormData()
    formData.append('file', file)

    return this.request<T>({
      url,
      method: 'POST',
      data: formData,
      headers: {
        // 注意：上传文件时不要设置Content-Type，浏览器会自动设置
      },
      ...config
    })
  }

  /**
   * 下载文件
   */
  async download(url: string, filename?: string, config?: Omit<RequestConfig, 'url' | 'method'>) {
    const blob = await this.request<Blob>({
      url,
      method: 'GET',
      responseType: 'blob',
      showLoading: true,
      showError: true,
      ...config
    })

    // 创建下载链接
    const downloadUrl = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = downloadUrl
    link.download = filename || 'download'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(downloadUrl)
  }

}

// 创建请求实例
const request = new Request()

export default request