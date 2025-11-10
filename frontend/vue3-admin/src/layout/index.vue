<template>
  <div class="arco-layout">
    <!-- 侧边栏 -->
    <aside class="sidebar" :class="{ 'sidebar-collapsed': isCollapsed }">
      <!-- Logo 区域 -->
      <div class="logo" @click="toggleCollapse">
        <div class="logo-icon">
          <el-icon><Monitor /></el-icon>
        </div>
        <span v-show="!isCollapsed" class="logo-text">KK Admin</span>
      </div>

      <!-- 导航菜单 -->
      <el-menu
        :default-active="activeMenu"
        :collapse="isCollapsed"
        :unique-opened="true"
        router
        class="sidebar-menu"
      >
        <el-sub-menu index="dashboard">
          <template #title>
            <el-icon><DataBoard /></el-icon>
            <span>仪表盘</span>
          </template>
          <el-menu-item index="/">
            <el-icon><HomeFilled /></el-icon>
            <span>工作台</span>
          </el-menu-item>
          <el-menu-item index="/analysis">
            <el-icon><TrendCharts /></el-icon>
            <span>数据分析</span>
          </el-menu-item>
        </el-sub-menu>

        <el-sub-menu index="system">
          <template #title>
            <el-icon><Setting /></el-icon>
            <span>系统管理</span>
          </template>
          <el-menu-item index="/users">
            <el-icon><User /></el-icon>
            <span>用户管理</span>
          </el-menu-item>
          <el-menu-item index="/roles">
            <el-icon><UserFilled /></el-icon>
            <span>角色管理</span>
          </el-menu-item>
          <el-menu-item index="/permissions">
            <el-icon><Lock /></el-icon>
            <span>权限管理</span>
          </el-menu-item>
        </el-sub-menu>

        <el-sub-menu index="content">
          <template #title>
            <el-icon><Files /></el-icon>
            <span>内容管理</span>
          </template>
          <el-menu-item index="/articles">
            <el-icon><Document /></el-icon>
            <span>文章管理</span>
          </el-menu-item>
          <el-menu-item index="/categories">
            <el-icon><Folder /></el-icon>
            <span>分类管理</span>
          </el-menu-item>
          <el-menu-item index="/tags">
            <el-icon><PriceTag /></el-icon>
            <span>标签管理</span>
          </el-menu-item>
        </el-sub-menu>

        <el-menu-item index="/settings">
          <el-icon><Tools /></el-icon>
          <template #title>系统设置</template>
        </el-menu-item>

        <el-menu-item index="/about">
          <el-icon><InfoFilled /></el-icon>
          <template #title>关于我们</template>
        </el-menu-item>
      </el-menu>

      <!-- 折叠按钮 -->
      <div class="collapse-trigger" @click="toggleCollapse">
        <el-icon>
          <component :is="isCollapsed ? 'Expand' : 'Fold'" />
        </el-icon>
        <span v-show="!isCollapsed" class="collapse-text">
          {{ isCollapsed ? '展开' : '折叠' }}
        </span>
      </div>
    </aside>

    <!-- 主内容区域 -->
    <div class="main-container">
      <!-- 顶部导航栏 -->
      <header class="header">
        <div class="header-left">
          <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/' }">
              <el-icon><HomeFilled /></el-icon>
              首页
            </el-breadcrumb-item>
            <el-breadcrumb-item v-for="item in breadcrumbs" :key="item.path">
              {{ item.meta?.title || item.name }}
            </el-breadcrumb-item>
          </el-breadcrumb>
        </div>

        <div class="header-right">

          <!-- 通知中心 -->
          <el-dropdown trigger="click" class="notification">
            <el-badge :value="unreadCount" :max="99" class="badge">
              <el-button icon="Bell" circle size="small" />
            </el-badge>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item>
                  <el-icon><ChatDotRound /></el-icon>
                  你有{{ unreadCount }}条新消息
                </el-dropdown-item>
                <el-dropdown-item divided>
                  <el-icon><View /></el-icon>
                  查看全部
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>

          <!-- 全屏切换 -->
          <el-tooltip :content="isFullscreen ? '退出全屏' : '全屏'" placement="bottom">
            <el-button 
              :icon="isFullscreen ? 'CopyDocument' : 'FullScreen'" 
              circle 
              size="small"
              @click="toggleFullscreen"
            />
          </el-tooltip>

          <!-- 用户信息 -->
          <el-dropdown class="user-dropdown">
            <el-button type="primary" link class="user-btn">
              <el-avatar :size="24" :src="userAvatar" class="user-avatar" />
              <span class="username">{{ userName }}</span>
              <el-icon><ArrowDown /></el-icon>
            </el-button>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item @click="goToProfile">
                  <el-icon><User /></el-icon>
                  个人中心
                </el-dropdown-item>
                <el-dropdown-item @click="changePassword">
                  <el-icon><EditPen /></el-icon>
                  修改密码
                </el-dropdown-item>
                <el-dropdown-item divided @click="logout">
                  <el-icon><SwitchButton /></el-icon>
                  退出登录
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </header>

      <!-- 标签页 -->
      <div class="tabs-container" v-if="showTabs">
        <div class="tabs-header">
          <el-tabs
            v-model="activeTab"
            type="card"
            closable
            @tab-click="handleTabClick"
            @tab-remove="handleTabRemove"
          >
            <el-tab-pane
              v-for="tab in tabs"
              :key="tab.name"
              :name="tab.name"
              :label="tab.meta?.title || tab.name"
            />
          </el-tabs>
          <el-button 
            icon="Refresh"
            circle 
            size="small" 
            class="refresh-btn"
            @click="refreshPage"
          />
        </div>
      </div>

      <!-- 主要内容 -->
      <main class="content">
        <div class="content-wrapper">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useUserStore } from '@/stores/user'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

// 响应式数据
const isCollapsed = ref(false)
const activeTab = ref('')
const showTabs = ref(true)
const tabs = ref<any[]>([])
const isFullscreen = ref(false)
const unreadCount = ref(5)

// 用户信息 - 从store中获取
const userName = computed(() => userStore.userInfo.username || '管理员')
const userAvatar = computed(() => userStore.userInfo.avatar || '')

// 计算属性
const activeMenu = computed(() => route.path)
const breadcrumbs = computed(() => {
  const matched = route.matched.filter(item => item.meta?.title)
  return matched.slice(1) // 去掉根路由
})

// 监听路由变化，更新标签页
watch(
  () => route,
  (newRoute) => {
    if (!newRoute.meta?.title) return

    const existingTab = tabs.value.find(tab => tab.name === newRoute.name)
    if (!existingTab) {
      tabs.value.push({
        name: newRoute.name,
        path: newRoute.path,
        meta: newRoute.meta
      })
    }
    activeTab.value = newRoute.name as string
  },
  { immediate: true, deep: true }
)

// 组件挂载时加载主题设置
onMounted(() => {
  // 监听全屏状态变化
  document.addEventListener('fullscreenchange', handleFullscreenChange)
})

// 组件卸载时移除事件监听
onUnmounted(() => {
  document.removeEventListener('fullscreenchange', handleFullscreenChange)
})

// 方法
const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
    isFullscreen.value = true
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen()
      isFullscreen.value = false
    }
  }
}

const handleFullscreenChange = () => {
  isFullscreen.value = !!document.fullscreenElement
}

const refreshPage = () => {
  window.location.reload()
}

const goToProfile = () => {
  ElMessage.info('跳转到个人中心')
}

const changePassword = () => {
  ElMessage.info('修改密码功能')
}

const logout = async () => {
  try {
    await ElMessageBox.confirm('确定要退出登录吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning',
    })
    
    // 调用用户store的退出登录方法（包含路由跳转）
    await userStore.logout()
    
    ElMessage.success('退出登录成功')
  } catch {
    // 用户取消操作
  }
}

const handleTabClick = (tab: any) => {
  const targetTab = tabs.value.find(t => t.name === tab.paneName)
  if (targetTab) {
    router.push(targetTab.path)
  }
}

const handleTabRemove = (tabName: string) => {
  const index = tabs.value.findIndex(tab => tab.name === tabName)
  if (index > -1) {
    tabs.value.splice(index, 1)
    // 如果关闭的是当前标签，跳转到上一个标签或首页
    if (activeTab.value === tabName) {
      const prevTab = tabs.value[index - 1] || tabs.value[0]
      if (prevTab) {
        router.push(prevTab.path)
      } else {
        router.push('/')
      }
    }
  }
}
</script>

<style scoped>
.arco-layout {
  height: 100vh;
  display: flex;
  background-color: #f5f5f5;
  transition: all 0.3s ease;
}

.arco-layout.dark-theme {
  background-color: #141414;
  color: #E5EAF3;
}

/* 侧边栏样式 */
.sidebar {
  width: 220px;
  background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  position: relative;
}

.sidebar-collapsed {
  width: 64px;
}

.logo {
  height: 64px;
  display: flex;
  align-items: center;
  padding: 0 20px;
  color: white;
  cursor: pointer;
  border-bottom: 1px solid #374151;
  transition: all 0.3s;
}

.logo:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.logo-icon {
  font-size: 24px;
  margin-right: 12px;
  color: #409eff;
}

.logo-text {
  font-size: 18px;
  font-weight: 600;
  white-space: nowrap;
}

.sidebar-menu {
  flex: 1;
  border: none;
  background: transparent;
}

.sidebar-menu :deep(.el-menu) {
  background: transparent;
}

.sidebar-menu :deep(.el-menu-item),
.sidebar-menu :deep(.el-sub-menu__title) {
  color: #d1d5db;
  height: 48px;
  line-height: 48px;
}

.sidebar-menu :deep(.el-menu-item:hover),
.sidebar-menu :deep(.el-sub-menu__title:hover) {
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
}

.sidebar-menu :deep(.el-menu-item.is-active) {
  background-color: #409eff;
  color: white;
}

.collapse-trigger {
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #d1d5db;
  cursor: pointer;
  border-top: 1px solid #374151;
  transition: all 0.3s;
  gap: 8px;
}

.collapse-trigger:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
}

.collapse-text {
  font-size: 12px;
  white-space: nowrap;
}

/* 主容器样式 */
.main-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* 顶部导航栏 */
.header {
  height: 64px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  z-index: 10;
  transition: all 0.3s ease;
}

.dark-theme .header {
  background: #1f1f1f;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

.header-left :deep(.el-breadcrumb) {
  font-size: 14px;
}

.header-left :deep(.el-breadcrumb__inner) {
  display: flex;
  align-items: center;
  gap: 4px;
}

.dark-theme .header-left :deep(.el-breadcrumb__inner),
.dark-theme .header-left :deep(.el-breadcrumb__separator) {
  color: #E5EAF3;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.theme-toggle {
  transition: all 0.3s ease;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 12px;
}

.user-avatar {
  margin-right: 4px;
}

.username {
  font-size: 14px;
  font-weight: 500;
}

.dark-theme .username {
  color: #E5EAF3;
}

/* 标签页样式 */
.tabs-container {
  background: white;
  padding: 0 24px;
  border-bottom: 1px solid #e4e7ed;
  transition: all 0.3s ease;
}

.dark-theme .tabs-container {
  background: #1f1f1f;
  border-bottom: 1px solid #414243;
}

.tabs-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 40px;
}

.tabs-header :deep(.el-tabs) {
  flex: 1;
}

.tabs-header :deep(.el-tabs__header) {
  margin: 0;
}

.tabs-header :deep(.el-tabs__item) {
  height: 32px;
  line-height: 32px;
  font-size: 12px;
}

.refresh-btn {
  margin-left: 8px;
}

/* 内容区域 */
.content {
  flex: 1;
  padding: 24px;
  overflow: auto;
}

.content-wrapper {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  min-height: calc(100vh - 200px);
  transition: all 0.3s ease;
}

.dark-theme .content-wrapper {
  background: #1f1f1f;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  color: #E5EAF3;
}

/* 过渡动画 */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>