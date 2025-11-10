<template>
  <div class="settings">
    <el-card header="系统设置">
      <el-form :model="form" label-width="120px">
        <el-form-item label="系统名称">
          <el-input v-model="form.systemName" placeholder="请输入系统名称" />
        </el-form-item>
        <el-form-item label="系统版本">
          <el-input v-model="form.version" placeholder="请输入系统版本" />
        </el-form-item>
        <el-form-item label="管理员邮箱">
          <el-input v-model="form.adminEmail" placeholder="请输入管理员邮箱" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSave">保存设置</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { ElMessage } from 'element-plus'

// 表单数据
const form = ref({
  systemName: 'KK Admin',
  version: '1.0.0',
  adminEmail: 'admin@example.com'
})

// 保存设置
const handleSave = () => {
  ElMessage.success('设置保存成功')
}

// 重置表单
const handleReset = () => {
  form.value = {
    systemName: 'KK Admin',
    version: '1.0.0',
    adminEmail: 'admin@example.com'
  }
}
</script>

<style scoped>
.settings {
  max-width: 800px;
  margin: 0 auto;
}
</style>