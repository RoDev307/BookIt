<template>
  <header class="topbar">
    <h1>Consola de Control</h1>

    <div class="topbar-actions">
      <button
        type="button"
        class="theme-button"
        :aria-label="isDark ? 'Activar tema claro' : 'Activar tema oscuro'"
        :aria-pressed="isDark"
        @click="toggleTheme"
      >
        <span aria-hidden="true">{{ isDark ? '☀️' : '🌙' }}</span>
        {{ isDark ? 'Tema claro' : 'Tema oscuro' }}
      </button>

      <span class="user-role">Administrador</span>

      <a
        href="https://book-it-five-nu.vercel.app/"
        target="_blank"
        rel="noopener noreferrer"
        class="public-link"
      >
        Ver sitio público →
      </a>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'

function readSavedTheme(): boolean {
  try {
    return localStorage.getItem('bookit-theme') === 'dark'
  } catch {
    return false
  }
}

const isDark = ref(readSavedTheme())

function applyTheme() {
  document.documentElement.dataset.theme = isDark.value ? 'dark' : 'light'
}

function toggleTheme() {
  isDark.value = !isDark.value
  applyTheme()

  try {
    localStorage.setItem('bookit-theme', isDark.value ? 'dark' : 'light')
  } catch {
    // Permite cambiar el tema aunque no se pueda guardar la preferencia.
  }
}

applyTheme()
</script>

<style scoped>
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  padding: 22px 28px;
  background: var(--surface-background, #ffffff);
  border-bottom: 1px solid var(--border-color, #e2e8f0);
  box-shadow: 0 2px 4px rgb(0 0 0 / 5%);
}

.topbar h1 {
  margin: 0;
  color: var(--text-primary, #0f172a);
  font-size: 24px;
  font-weight: 700;
}

.topbar-actions {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.theme-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 14px;
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 20px;
  background: var(--surface-secondary, #f8fafc);
  color: var(--text-primary, #0f172a);
  font: inherit;
  font-size: 13px;
  cursor: pointer;
}

.user-role {
  color: var(--text-secondary, #64748b);
  font-size: 16px;
  font-weight: 600;
}

.public-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 20px;
  border-radius: 12px;
  background: var(--primary-color, #4f35f5);
  color: #ffffff;
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
}

.public-link:hover {
  background: var(--primary-hover, #4338ca);
}

.theme-button:focus-visible,
.public-link:focus-visible {
  outline: 3px solid #a5b4fc;
  outline-offset: 3px;
}

@media (max-width: 640px) {
  .topbar {
    padding: 18px;
  }

  .topbar h1 {
    font-size: 20px;
  }

  .topbar-actions {
    gap: 12px;
  }
}
</style>
