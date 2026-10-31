<template>
  <div class="admin-layout">
    <AdminSidebar />

    <div class="main-section">
      <AdminTopbar />

      <main class="page-content">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
import AdminSidebar from '../components/layout/AdminSidebar.vue'
import AdminTopbar from '../components/layout/AdminTopbar.vue'
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: var(--page-background);
}

.admin-layout > :first-child {
  flex-shrink: 0;
}

.main-section {
  flex: 1;
  min-width: 0;
}

.page-content {
  padding: 40px;
}

@media (max-width: 768px) {
  .admin-layout {
    flex-direction: column;
  }

  .admin-layout > :first-child {
    width: 100%;
    min-height: auto;
  }

  .page-content {
    padding: 20px;
  }
}
</style>
