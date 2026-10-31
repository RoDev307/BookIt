<template>
  <section>
    <header class="page-heading">
      <h2>Registrar nuevo negocio</h2>
      <p>Crea un comercio y la cuenta de su administrador.</p>
    </header>

    <p v-if="showNotice" class="integration-notice" role="status">
      El guardado estará disponible al completar la integración con la API.
    </p>

    <BusinessForm @submit="handleSubmit" />
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BusinessForm from '../../components/superadmin/BusinessForm.vue'

const showNotice = ref(false)

function handleSubmit() {
  // Pendiente: enviar los datos a POST /api/v1/superadmin/businesses.
  showNotice.value = true
}
</script>

<style scoped>
.page-heading {
  margin-bottom: 28px;
}

.page-heading h2 {
  margin: 0 0 12px;
  color: var(--text-primary);
  font-size: 30px;
  font-weight: 800;
}

.page-heading p {
  margin: 0;
  color: var(--text-secondary);
  font-size: 16px;
  line-height: 1.6;
}

.integration-notice {
  margin: 0 0 20px;
  padding: 16px;
  border: 1px solid var(--info-border);
  border-radius: 10px;
  background: var(--info-background);
  color: var(--info-text);
  font-size: 14px;
  line-height: 1.6;
}

@media (max-width: 600px) {
  .page-heading h2 {
    font-size: 24px;
  }
}
</style>
