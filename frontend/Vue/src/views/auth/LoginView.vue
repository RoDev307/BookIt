<template>
  <main class="login-page">
    <section class="login-card">
      <div class="theme-actions">
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
      </div>

      <div class="brand">BookIt Admin</div>

      <h1>Consola de acceso</h1>

      <p class="description">
        Ingresa tus credenciales para acceder al panel de superadministrador.
      </p>

      <form @submit.prevent="handleSubmit">
        <div class="field">
          <label for="email">Correo electrónico</label>
          <input
            id="email"
            v-model="email"
            type="email"
            autocomplete="username"
            placeholder="admin@ejemplo.com"
            required
          />
        </div>

        <div class="field">
          <label for="password">Contraseña</label>

          <div class="password-container">
            <input
              id="password"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              required
            />

            <button
              type="button"
              class="visibility-button"
              :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
              :aria-pressed="showPassword"
              aria-controls="password"
              @click="showPassword = !showPassword"
            >
              {{ showPassword ? 'Ocultar' : 'Mostrar' }}
            </button>
          </div>
        </div>

        <p v-if="showMessage" class="form-message" role="status">
          El inicio de sesión estará disponible al completar la integración con la API.
        </p>

        <button type="submit" class="login-button">Iniciar sesión</button>
      </form>

      <RouterLink to="/superadmin/dashboard" class="preview-link">
        Vista previa del panel →
      </RouterLink>
    </section>
  </main>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const showMessage = ref(false)

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
    // El tema funciona aunque no se pueda guardar la preferencia.
  }
}

function handleSubmit() {
  // Pendiente: autenticar mediante POST /api/v1/auth/login.
  // Validar el rol super_admin antes de permitir el acceso al panel.
  showMessage.value = true
  password.value = ''
}

applyTheme()
</script>

<style scoped>
.login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 24px;
  background: var(--page-background);
}

.login-card {
  width: 100%;
  max-width: 460px;
  padding: 36px;
  border: 1px solid var(--border-color);
  border-radius: 20px;
  background: var(--surface-background);
  box-shadow: 0 8px 30px rgb(15 23 42 / 8%);
}

.theme-actions {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 24px;
}

.theme-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 12px;
  border: 1px solid var(--border-color);
  border-radius: 20px;
  background: var(--surface-secondary);
  color: var(--text-primary);
  font: inherit;
  font-size: 13px;
  cursor: pointer;
}

.brand {
  margin-bottom: 28px;
  color: var(--primary-color);
  font-size: 28px;
  font-weight: 800;
}

h1 {
  margin: 0 0 12px;
  color: var(--text-primary);
  font-size: 26px;
}

.description {
  margin: 0 0 24px;
  color: var(--text-secondary);
  font-size: 15px;
  line-height: 1.6;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

label {
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 600;
}

input {
  width: 100%;
  min-width: 0;
  padding: 13px;
  border: 1px solid var(--input-border);
  border-radius: 9px;
  background: var(--surface-secondary);
  color: var(--text-primary);
  font: inherit;
}

input::placeholder {
  color: var(--text-secondary);
  opacity: 1;
}

input:focus {
  border-color: var(--primary-color);
  outline: 2px solid var(--primary-color);
  outline-offset: 2px;
}

.password-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.visibility-button {
  flex-shrink: 0;
  padding: 10px 8px;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--surface-secondary);
  color: var(--text-primary);
  font: inherit;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

.login-button {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 10px;
  background: var(--primary-color);
  color: #ffffff;
  font: inherit;
  font-weight: 700;
  cursor: pointer;
}

.login-button:hover {
  background: var(--primary-hover);
}

.form-message {
  margin: 0 0 20px;
  color: var(--text-secondary);
  font-size: 13px;
  line-height: 1.6;
}

.preview-link {
  display: block;
  margin-top: 24px;
  color: var(--text-primary);
  text-align: center;
  text-decoration: underline;
  text-underline-offset: 4px;
  font-size: 14px;
  font-weight: 600;
}

button:focus-visible,
a:focus-visible {
  outline: 3px solid #a5b4fc;
  outline-offset: 3px;
}

@media (max-width: 480px) {
  .login-card {
    padding: 24px;
  }
}
</style>
