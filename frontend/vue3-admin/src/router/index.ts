import { createRouter, createWebHistory } from 'vue-router'
import BasicLayout from '@/layout/index.vue'
import { useUserStore } from '@/stores/user'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/login/index.vue'),
      meta: { 
        title: '登录',
        requiresAuth: false // 登录页面不需要认证
      }
    },
    {
      path: '/',
      component: BasicLayout,
      redirect: '/home',
      meta: { requiresAuth: true }, // 需要认证的路由
      children: [
        {
          path: 'home',
          name: 'Home',
          component: () => import('@/views/home/index.vue'),
          meta: { title: '首页' }
        },
        {
          path: 'settings',
          name: 'Settings',
          component: () => import('@/views/sys/setting.vue'),
          meta: { title: '系统设置' }
        }
      ]
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/404.vue'),
      meta: { title: '页面未找到' }
    }
  ]
})

// 路由守卫
router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  
  // 初始化登录状态
  if (!userStore.isLoggedIn) {
    userStore.initAuth()
  }

  // 设置页面标题
  if (to.meta.title) {
    document.title = `${to.meta.title} - KK Admin`
  }

  // 检查路由是否需要认证
  if (to.meta.requiresAuth) {
    if (userStore.isAuthenticated) {
      // 已登录，允许访问
      next()
    } else {
      // 未登录，重定向到登录页面
      next({
        path: '/login',
        query: { redirect: to.fullPath } // 保存当前路径，登录后跳转
      })
    }
  } else if (to.path === '/login' && userStore.isAuthenticated) {
    // 已登录但访问登录页面，重定向到首页
    next({ path: '/' })
  } else {
    // 不需要认证的路由，直接放行
    next()
  }
})

// 路由后置守卫（可用于页面统计等）
router.afterEach((to, from) => {
  // 可以在这里添加页面访问统计等逻辑
  console.log(`路由跳转: ${from.path} -> ${to.path}`)
})

export default router