<template>
  <aside class="sidebar">
    <div class="brand">BookIt Admin</div>

    <nav class="navigation" aria-label="Menú del superadministrador">
      <RouterLink to="/superadmin/dashboard" class="nav-link">
        <span>📊 Métricas</span>
      </RouterLink>

      <div class="divider"></div>

      <p class="section-label">ADMINISTRADOR MAESTRO</p>

      <RouterLink
        to="/superadmin/businesses"
        class="nav-link"
        :class="{ 'router-link-active': isBusinessesSection }"
      >
        <span>Controlar Comercios</span>
        <span class="badge">ROOT</span>
      </RouterLink>
    </nav>

    <footer class="sidebar-footer">
      <p>👤 Administrador Maestro</p>
    </footer>
  </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()

const isBusinessesSection = computed(() => {
  return route.path === '/superadmin/businesses' || route.path.startsWith('/superadmin/businesses/')
})
</script>

<style scoped>
.sidebar {
  width: 260px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #1e293b;
  color: #cbd5e1;
}

.brand {
  padding: 28px 24px;
  color: #818cf8;
  font-size: 28px;
  font-weight: 800;
}

.navigation {
  flex: 1;
  padding: 20px;
}

.nav-link {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px;
  border-radius: 8px;
  color: #cbd5e1;
  text-decoration: none;
  font-size: 17px;
  font-weight: 600;
}

.nav-link:hover {
  background: #334155;
}

.nav-link.router-link-active {
  background: #3730a3;
  color: #ffffff;
}

.divider {
  height: 1px;
  margin: 26px 0;
  background: #334155;
}

.section-label {
  margin: 0 12px 18px;
  color: #94a3b8;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1.5px;
}

.badge {
  padding: 4px 7px;
  border-radius: 5px;
  background: #6366f1;
  color: #ffffff;
  font-size: 11px;
  letter-spacing: 1px;
}

.sidebar-footer {
  padding: 24px;
  border-top: 1px solid #334155;
  color: #94a3b8;
  font-size: 14px;
}

.sidebar-footer p {
  margin: 0;
}

.sidebar-footer .session-note {
  margin-top: 10px;
  font-size: 12px;
}

@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    min-height: auto;
  }

  .brand {
    padding: 16px 20px;
    font-size: 22px;
  }

  .navigation {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 0 16px 16px;
  }

  .nav-link {
    flex: 1;
    padding: 12px;
    font-size: 14px;
  }

  .divider,
  .section-label,
  .sidebar-footer {
    display: none;
  }

  .badge {
    font-size: 10px;
  }
}
</style>
