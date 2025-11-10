<template>
  <div class="not-found-container">
    <div class="not-found-content">
      <el-icon class="not-found-icon"><Warning /></el-icon>
      <h1 class="not-found-title">404</h1>
      <p class="not-found-desc">抱歉，您访问的页面不存在</p>
      <el-button type="primary" @click="goHome">返回首页</el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const goHome = () => {
  router.push('/')
}
</script>

<style scoped>
.not-found-container {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f7fa;
}

.not-found-content {
  text-align: center;
}

.not-found-icon {
  font-size: 80px;
  color: #909399;
  margin-bottom: 20px;
}

.not-found-title {
  font-size: 48px;
  color: #303133;
  margin: 0 0 16px 0;
}

.not-found-desc {
  font-size: 16px;
  color: #606266;
  margin: 0 0 24px 0;
}
</style>