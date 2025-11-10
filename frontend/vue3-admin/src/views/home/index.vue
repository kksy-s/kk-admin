<template>
  <div class="home">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>欢迎回来，管理员</h2>
        <p>今天是 {{ todayDate }}，祝您工作愉快</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" icon="Refresh" @click="refreshDashboard">
          刷新数据
        </el-button>
        <el-button type="default" icon="Download" @click="exportData">
          导出报表
        </el-button>
      </div>
    </div>

    <!-- 统计卡片区域 -->
    <div class="stats-section">
      <el-row :gutter="20">
        <el-col :xs="24" :sm="12" :lg="6" v-for="stat in statistics" :key="stat.id">
          <el-card class="stat-card" hoverable shadow="hover">
            <div class="stat-content">
              <div class="stat-icon" :style="{ backgroundColor: stat.color }">
                <el-icon><component :is="stat.icon" /></el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ stat.value }}</div>
                <div class="stat-label">{{ stat.label }}</div>
                <div class="stat-change" :class="stat.trend === 'up' ? 'text-success' : 'text-danger'">
                  <el-icon><ArrowUp v-if="stat.trend === 'up'" /><ArrowDown v-else /></el-icon>
                  {{ stat.change }}%
                </div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 图表和数据展示区域 -->
    <div class="data-section">
      <el-row :gutter="20">
        <!-- 销售趋势图表 -->
        <el-col :xs="24" :lg="16">
          <el-card header="销售趋势" class="data-card">
            <div class="chart-container" style="height: 300px;">
              <!-- 这里可以放置实际的图表组件 -->
              <div class="chart-placeholder">销售趋势图表区域</div>
            </div>
          </el-card>
        </el-col>
        
        <!-- 收入分布 -->
        <el-col :xs="24" :lg="8">
          <el-card header="收入分布" class="data-card">
            <div class="chart-container" style="height: 300px;">
              <!-- 这里可以放置实际的图表组件 -->
              <div class="chart-placeholder">收入分布图表区域</div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 快速操作和最近活动 -->
    <div class="bottom-section">
      <el-row :gutter="20">
        <!-- 快速操作 -->
        <el-col :xs="24" :lg="8">
          <el-card header="快速操作" class="operation-card">
            <div class="quick-actions">
              <div 
                v-for="action in quickActions" 
                :key="action.name"
                class="action-item"
                @click="handleAction(action)"
              >
                <div class="action-icon" :style="{ backgroundColor: action.color }">
                  <el-icon><component :is="action.icon" /></el-icon>
                </div>
                <div class="action-name">{{ action.name }}</div>
              </div>
            </div>
          </el-card>
        </el-col>
        
        <!-- 最近活动 -->
        <el-col :xs="24" :lg="16">
          <el-card header="最近活动" class="activities-card">
            <div class="activities-list">
              <el-timeline>
                <el-timeline-item
                  v-for="activity in recentActivities"
                  :key="activity.id"
                  :timestamp="formatActivityTime(activity.time)"
                  :type="activity.type"
                  placement="top"
                >
                  <div class="activity-content">
                    <div class="activity-text">{{ activity.content }}</div>
                    <div class="activity-user">{{ activity.user || '系统' }}</div>
                  </div>
                </el-timeline-item>
              </el-timeline>
            </div>
            <div class="view-all">
              <el-link type="primary" @click="viewAllActivities">查看全部</el-link>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup lang="ts">
defineOptions({ name: 'HomeIndexView' });

import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'

const router = useRouter()

// 今天的日期
const todayDate = computed(() => {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  const weekday = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'][now.getDay()]
  return `${year}-${month}-${day} ${weekday}`
})

// 统计数据 - 使用全局注册的图标名称
const statistics = ref([
  { id: 1, value: '1,234', label: '用户总数', icon: 'User', color: '#409eff', trend: 'up', change: '12.5' },
  { id: 2, value: '5,678', label: '订单数量', icon: 'ShoppingCart', color: '#67c23a', trend: 'up', change: '8.2' },
  { id: 3, value: '¥89,012', label: '今日收入', icon: 'Wallet', color: '#e6a23c', trend: 'down', change: '2.1' },
  { id: 4, value: '345', label: '未读消息', icon: 'Message', color: '#f56c6c', trend: 'up', change: '5.7' }
])

// 快速操作列表 - 使用全局注册的图标名称
const quickActions = ref([
  { name: '新增用户', icon: 'Plus', color: '#67c23a', route: '/users/create' },
  { name: '发布公告', icon: 'FileText', color: '#409eff', route: '/announcements/create' },
  { name: '查看消息', icon: 'Bell', color: '#f56c6c', route: '/messages' },
  { name: '系统设置', icon: 'Settings', color: '#e6a23c', route: '/settings' }
])

// 最近活动数据
const recentActivities = ref([
  { id: 1, time: '2024-01-15 14:30', content: '登录系统', user: '管理员', type: 'primary' },
  { id: 2, time: '2024-01-15 14:00', content: '修改了个人信息', user: '张三', type: 'success' },
  { id: 3, time: '2024-01-15 13:45', content: '新增了10条订单记录', user: '系统', type: 'warning' },
  { id: 4, time: '2024-01-15 13:30', content: '系统备份完成', user: '系统', type: 'info' },
  { id: 5, time: '2024-01-15 13:00', content: '修改了系统配置', user: '管理员', type: 'primary' }
])

// 组件挂载时的操作
onMounted(() => {
  // 这里可以添加获取数据的逻辑
})

// 处理快速操作点击
const handleAction = (action: { name: string; route?: string }) => {
  console.log('执行操作:', action.name)
  if (action.route) {
    router.push(action.route)
  } else {
    ElMessage.info(`执行${action.name}操作`)
  }
}

// 刷新仪表盘数据
const refreshDashboard = () => {
  ElMessage.info('正在刷新数据...')
  // 这里可以添加刷新数据的逻辑
  setTimeout(() => {
    ElMessage.success('数据刷新成功')
  }, 1000)
}

// 导出报表
const exportData = () => {
  ElMessage.info('正在准备导出报表...')
  // 这里可以添加导出报表的逻辑
  setTimeout(() => {
    ElMessage.success('报表导出成功')
  }, 1500)
}

// 查看全部活动
const viewAllActivities = () => {
  ElMessage.info('跳转到活动日志页面')
  // 这里可以添加跳转到活动日志页面的逻辑
}

// 格式化活动时间
const formatActivityTime = (timeStr: string) => {
  const time = new Date(timeStr)
  const now = new Date()
  const diffMs = now.getTime() - time.getTime()
  const diffMins = Math.floor(diffMs / (1000 * 60))
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  
  if (diffMins < 60) {
    return `${diffMins}分钟前`
  } else if (diffHours < 24) {
    return `${diffHours}小时前`
  } else if (diffDays < 7) {
    return `${diffDays}天前`
  } else {
    return timeStr
  }
}
</script>

<style scoped>
.home {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

/* 页面头部 */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  color: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.header-content h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  opacity: 0.9;
}

.header-actions .el-button {
  margin-left: 12px;
  transition: all 0.3s ease;
}

.header-actions .el-button:hover {
  transform: translateY(-2px);
}

/* 统计卡片 */
.stats-section {
  margin-bottom: 30px;
}

.stat-card {
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.stat-content {
  display: flex;
  align-items: center;
  padding: 16px;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin-right: 16px;
}

.stat-icon .el-icon {
  font-size: 20px;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #606266;
  margin-bottom: 4px;
}

.stat-change {
  font-size: 12px;
  display: flex;
  align-items: center;
}

.stat-change .el-icon {
  font-size: 12px;
  margin-right: 4px;
}

/* 数据展示区域 */
.data-section {
  margin-bottom: 30px;
}

.data-card {
  border-radius: 12px;
  overflow: hidden;
  height: 100%;
}

.data-card .el-card__header {
  padding: 16px;
  font-weight: 600;
  border-bottom: 1px solid #f0f0f0;
}

.data-card .el-card__body {
  padding: 0;
}

.chart-container {
  position: relative;
  width: 100%;
}

.chart-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #909399;
  font-size: 14px;
}

/* 底部区域 */
.bottom-section {
  margin-bottom: 20px;
}

/* 快速操作 */
.operation-card {
  border-radius: 12px;
  height: 100%;
}

.operation-card .el-card__header {
  padding: 16px;
  font-weight: 600;
  border-bottom: 1px solid #f0f0f0;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  padding: 16px;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px;
  border-radius: 8px;
  background-color: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-item:hover {
  background-color: #e9ecef;
  transform: translateY(-2px);
}

.action-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin-bottom: 8px;
}

.action-icon .el-icon {
  font-size: 18px;
}

.action-name {
  font-size: 14px;
  color: #303133;
}

/* 最近活动 */
.activities-card {
  border-radius: 12px;
  height: 100%;
}

.activities-card .el-card__header {
  padding: 16px;
  font-weight: 600;
  border-bottom: 1px solid #f0f0f0;
}

.activities-list {
  max-height: 400px;
  overflow-y: auto;
  padding: 0 16px 16px 16px;
}

.activities-list .el-timeline {
  padding-left: 0;
}

.activity-content {
  padding: 8px 12px;
  background-color: #f8f9fa;
  border-radius: 6px;
}

.activity-text {
  font-size: 14px;
  color: #303133;
  margin-bottom: 4px;
}

.activity-user {
  font-size: 12px;
  color: #909399;
}

.view-all {
  text-align: center;
  padding: 12px 0;
  border-top: 1px solid #f0f0f0;
}

/* 响应式设计 */
@media screen and (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .header-actions {
    margin-top: 16px;
    width: 100%;
    display: flex;
  }
  
  .header-actions .el-button {
    flex: 1;
    margin-left: 0;
    margin-right: 8px;
  }
  
  .header-actions .el-button:last-child {
    margin-right: 0;
  }
  
  .quick-actions {
    grid-template-columns: 1fr;
  }
}
</style>