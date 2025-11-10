import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authAPI } from '@/api'
import router from '@/router'

export const useUserStore = defineStore('user', () => {
  // 用户信息
  const userInfo = ref({
    id: '',
    username: '',
    role: '',
    avatar: '',
    nickname: ''
  })

  // 登录状态
  const isLoggedIn = ref(false)

  // 登录令牌
  const token = ref('')

  // 用户权限列表
  const permissions = ref<string[]>([])

  // 检查是否已登录的计算属性
  const isAuthenticated = computed(() => {
    return isLoggedIn.value && token.value
  })

  // 登录方法（添加remember参数）
  const login = async (username: string, password: string, remember: boolean = false) => {
    try {
      const result = await authAPI.login(username, password)
      
      token.value = result.token
      userInfo.value = result.user_info
      isLoggedIn.value = true
      
      // 保存token到本地存储
      localStorage.setItem('kk-admin-token', token.value)
      localStorage.setItem('kk-admin-user', JSON.stringify(userInfo.value))
      
      // 如果选择记住密码，保存用户名和密码到localStorage
      if (remember) {
        // 使用简单的base64编码存储密码（注意：这不是加密，只是基本编码）
        const encodedPassword = btoa(password)
        localStorage.setItem('kk-admin-remembered-username', username)
        localStorage.setItem('kk-admin-remembered-password', encodedPassword)
      } else {
        // 不记住密码时清除存储
        localStorage.removeItem('kk-admin-remembered-username')
        localStorage.removeItem('kk-admin-remembered-password')
      }
      
      return { success: true }
    } catch (error: any) {
      return { success: false, message: error.message || '登录失败' }
    }
  }

  // 获取记住的用户名和密码
  const getRememberedCredentials = () => {
    const username = localStorage.getItem('kk-admin-remembered-username') || ''
    const encodedPassword = localStorage.getItem('kk-admin-remembered-password') || ''
    
    // 解码密码
    const password = encodedPassword ? atob(encodedPassword) : ''
    
    return {
      username,
      password,
      hasRemembered: !!(username && password)
    }
  }

  // 清除记住的密码
  const clearRememberedCredentials = () => {
    localStorage.removeItem('kk-admin-remembered-username')
    localStorage.removeItem('kk-admin-remembered-password')
  }

  // 登出方法
  const logout = async () => {
    try {
      // 调用后端退出接口
      await authAPI.logout()
    } catch (error) {
      console.error('退出登录调用失败:', error)
    } finally {
      // 清除本地状态
      token.value = ''
      userInfo.value = {
        id: '',
        username: '',
        role: '',
        avatar: '',
        nickname: ''
      }
      isLoggedIn.value = false
      permissions.value = []
      
      // 清除认证信息，但保留记住的用户名和密码
      localStorage.removeItem('kk-admin-token')
      localStorage.removeItem('kk-admin-user')
      
      // 使用路由实例跳转到登录页面
      router.push('/login')
    }
  }

  // 刷新token
  const refreshToken = async () => {
    try {
      const result = await authAPI.refreshToken()
      token.value = result.token
      localStorage.setItem('kk-admin-token', token.value)
      return { success: true }
    } catch (error: any) {
      return { success: false, message: error.message || 'Token刷新失败' }
    }
  }

  // 获取用户信息
  const getUserInfo = async () => {
    try {
      const result = await authAPI.getUserInfo()
      userInfo.value = result
      permissions.value = result.permissions || []
      localStorage.setItem('kk-admin-user', JSON.stringify(userInfo.value))
      return { success: true }
    } catch (error: any) {
      return { success: false, message: error.message || '获取用户信息失败' }
    }
  }

  // 检查权限
  const hasPermission = (permission: string) => {
    return permissions.value.includes(permission)
  }

  // 检查多个权限（需要全部满足）
  const hasPermissions = (requiredPermissions: string[]) => {
    return requiredPermissions.every(permission => 
      permissions.value.includes(permission)
    )
  }

  // 检查任意权限（满足其中一个即可）
  const hasAnyPermission = (requiredPermissions: string[]) => {
    return requiredPermissions.some(permission => 
      permissions.value.includes(permission)
    )
  }

  // 初始化登录状态
  const initAuth = () => {
    const savedToken = localStorage.getItem('kk-admin-token')
    const savedUser = localStorage.getItem('kk-admin-user')
    
    if (savedToken && savedUser) {
      try {
        token.value = savedToken
        userInfo.value = JSON.parse(savedUser)
        isLoggedIn.value = true
        
        // 自动获取最新的用户信息
        getUserInfo()
      } catch (error) {
        console.error('初始化登录状态失败:', error)
        logout()
      }
    }
  }

  return {
    userInfo,
    isLoggedIn,
    token,
    permissions,
    isAuthenticated,
    login,
    logout,
    refreshToken,
    getUserInfo,
    hasPermission,
    hasPermissions,
    hasAnyPermission,
    initAuth,
    getRememberedCredentials,  // 获取记住的凭据
    clearRememberedCredentials // 清除记住的凭据
  }
})