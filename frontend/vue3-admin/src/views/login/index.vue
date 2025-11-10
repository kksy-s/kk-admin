<template>
  <div class="login-container">
    <!-- 背景区域 -->
    <div class="login-bg">
      <div class="bg-overlay"></div>
      <div class="bg-content">
        <h1 class="welcome-title">欢迎使用 KK Admin</h1>
        <p class="welcome-desc">专业的企业级后台管理系统</p>
      </div>
    </div>

    <!-- 登录表单区域 -->
    <div class="login-form-container">
      <div class="login-form-wrapper">
        <!-- Logo 区域 -->
        <div class="form-logo">
          <el-icon class="logo-icon"><Monitor /></el-icon>
          <span class="logo-text">KK Admin</span>
        </div>

        <!-- 登录表单 -->
        <el-form
          ref="loginFormRef"
          :model="loginForm"
          :rules="loginRules"
          class="login-form"
          size="large"
        >
          <h2 class="form-title">用户登录</h2>
          
          <!-- 用户名输入 -->
          <el-form-item prop="username">
            <el-input
              v-model="loginForm.username"
              placeholder="请输入用户名"
              prefix-icon="User"
              clearable
            />
          </el-form-item>

          <!-- 密码输入 -->
          <el-form-item prop="password">
            <el-input
              v-model="loginForm.password"
              type="password"
              placeholder="请输入密码"
              prefix-icon="Lock"
              show-password
              clearable
              @keyup.enter="handleLogin"
            />
          </el-form-item>

          <!-- 记住我 -->
          <div class="form-options">
            <el-checkbox v-model="loginForm.remember">记住密码</el-checkbox>
            <el-button 
              v-if="loginForm.remember" 
              type="text" 
              size="small" 
              @click="clearRemembered"
              class="clear-remembered"
            >
              清除记住的密码
            </el-button>
          </div>

          <!-- 登录按钮 -->
          <el-form-item>
            <el-button
              type="primary"
              class="login-btn"
              :loading="loading"
              @click="handleLogin"
            >
              {{ loading ? '登录中...' : '登录' }}
            </el-button>
          </el-form-item>
        </el-form>

        <!-- 底部信息 -->
        <div class="form-footer">
          <p class="copyright">© 2024 KK Admin 版权所有</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const userStore = useUserStore()

const loginForm = ref({
  username: '',
  password: '',
  remember: false
})

const loading = ref(false)

// 登录表单验证规则
const loginRules = ref({
  username: [
    { required: true, message: '请输入用户名', trigger: 'blur' },
    { min: 3, max: 20, message: '用户名长度在 3 到 20 个字符', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, max: 20, message: '密码长度在 6 到 20 个字符', trigger: 'blur' }
  ]
})

// 页面加载时检查是否有记住的用户名和密码
onMounted(() => {
  const remembered = userStore.getRememberedCredentials()
  if (remembered.hasRemembered) {
    loginForm.value.username = remembered.username
    loginForm.value.password = remembered.password
    loginForm.value.remember = true
  }
})

// 登录处理
const handleLogin = async () => {
  if (!loginForm.value.username || !loginForm.value.password) {
    ElMessage.error('请输入用户名和密码')
    return
  }

  loading.value = true
  try {
    const result = await userStore.login(
      loginForm.value.username, 
      loginForm.value.password,
      loginForm.value.remember  // 传递记住密码选项
    )
    
    if (result.success) {
      ElMessage.success('登录成功')
      router.push('/')
    } else {
      ElMessage.error(result.message || '登录失败')
    }
  } catch (error: any) {
    ElMessage.error(error.message || '登录失败')
  } finally {
    loading.value = false
  }
}

// 清除记住的密码
const clearRemembered = () => {
  userStore.clearRememberedCredentials()
  loginForm.value.username = ''
  loginForm.value.password = ''
  loginForm.value.remember = false
  ElMessage.success('已清除记住的密码')
}
</script>

<style scoped>
.login-container {
  height: 100vh;
  display: flex;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
}

/* 背景区域 */
.login-bg {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  padding: 40px;
}

.bg-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
}

.bg-content {
  position: relative;
  z-index: 1;
  text-align: center;
}

.welcome-title {
  font-size: 48px;
  font-weight: 600;
  margin-bottom: 16px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.welcome-desc {
  font-size: 18px;
  opacity: 0.9;
  margin: 0;
}

/* 登录表单区域 */
.login-form-container {
  width: 480px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
  box-shadow: -2px 0 20px rgba(0, 0, 0, 0.1);
}

.login-form-wrapper {
  width: 100%;
  max-width: 320px;
}

/* Logo 区域 */
.form-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 40px;
  gap: 12px;
}

.logo-icon {
  font-size: 32px;
  color: #409eff;
}

.logo-text {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
}

/* 表单样式 */
.login-form {
  width: 100%;
}

.form-title {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  text-align: center;
  margin: 0 0 32px 0;
}

:deep(.el-form-item) {
  margin-bottom: 24px;
}

:deep(.el-input__wrapper) {
  border-radius: 6px;
}

:deep(.el-input__prefix) {
  color: #909399;
}

/* 表单选项 */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

:deep(.el-checkbox) {
  color: #606266;
}

/* 登录按钮 */
.login-btn {
  width: 100%;
  height: 44px;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 500;
}

/* 底部信息 */
.form-footer {
  margin-top: 32px;
  text-align: center;
}

.form-footer p {
  margin: 8px 0;
  color: #909399;
  font-size: 14px;
}

.copyright {
  margin-top: 16px;
  opacity: 0.7;
}

/* 响应式设计 */
@media screen and (max-width: 768px) {
  .login-container {
    flex-direction: column;
  }
  
  .login-bg {
    display: none;
  }
  
  .login-form-container {
    width: 100%;
    padding: 24px;
  }
  
  .welcome-title {
    font-size: 32px;
  }
  
  .welcome-desc {
    font-size: 16px;
  }
}

@media screen and (max-width: 480px) {
  .login-form-wrapper {
    max-width: 100%;
  }
  
  .form-logo {
    margin-bottom: 24px;
  }
  
  .form-title {
    font-size: 20px;
    margin-bottom: 24px;
  }
}
</style>

/* 添加清除记住密码按钮的样式 */
.clear-remembered {
  margin-left: 8px;
  color: #999;
  font-size: 12px;
}

.clear-remembered:hover {
  color: #666;
}