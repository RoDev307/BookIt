<template>
  <section>
    <header class="page-heading">
      <h2>Modificar comercio</h2>
      <p>Actualiza los datos del negocio y de su administrador.</p>
    </header>

    <template v-if="business">
      <p v-if="showNotice" class="integration-notice" role="status">
        El guardado estará disponible al completar la integración con la API.
      </p>

      <BusinessForm
        :key="businessId"
        :is-editing="true"
        :initial-data="business"
        @submit="handleSubmit"
      />
    </template>

    <div v-else class="not-found" role="status">
      <p>No encontramos ese comercio.</p>

      <RouterLink to="/superadmin/businesses"> ← Volver a comercios </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import BusinessForm from '../../components/superadmin/BusinessForm.vue'

interface BusinessExample {
  business_name: string
  email: string
  description: string
  image_url: string
  admin_name: string
  admin_email: string
}

const route = useRoute()
const showNotice = ref(false)

// Datos temporales para visualizar el formulario.
// Pendiente: cargar el comercio con GET /api/v1/superadmin/businesses/{id}.
const businesses: Record<number, BusinessExample> = {
  1: {
    business_name: 'Barbería de x',
    email: 'contacto@barberia.example',
    description: 'Servicios de barbería y cuidado personal.',
    image_url: '',
    admin_name: 'Administrador de Barbería',
    admin_email: 'admin@barberia.example',
  },
  2: {
    business_name: 'Clínica Dental Sonrisas',
    email: 'contacto@sonrisas.example',
    description: 'Servicios de atención y cuidado dental.',
    image_url: '',
    admin_name: 'Administrador de Clínica',
    admin_email: 'admin@sonrisas.example',
  },
  3: {
    business_name: 'Taller AutoFix',
    email: 'contacto@autofix.example',
    description: 'Mantenimiento y reparación de vehículos.',
    image_url: '',
    admin_name: 'Administrador de Taller',
    admin_email: 'admin@autofix.example',
  },
  4: {
    business_name: 'Hotel Pariston',
    email: 'contacto@pariston.example',
    description: 'Servicios de hospedaje y atención a huéspedes.',
    image_url: '',
    admin_name: 'Administrador de Hotel',
    admin_email: 'admin@pariston.example',
  },
  5: {
    business_name: 'Restaurante Premium',
    email: 'contacto@restaurante.example',
    description: 'Reservaciones y servicios de restaurante.',
    image_url: '',
    admin_name: 'Administrador de Restaurante',
    admin_email: 'admin@restaurante.example',
  },
}

const businessId = computed(() => Number(route.params.id))

const business = computed(() => {
  if (!Number.isInteger(businessId.value)) {
    return undefined
  }

  return businesses[businessId.value]
})

watch(businessId, () => {
  showNotice.value = false
})

function handleSubmit() {
  // Pendiente: enviar los cambios a PUT /api/v1/superadmin/businesses/{id}.
  // El backend espera "name" en edición, en lugar de "business_name".
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

.not-found {
  padding: 24px;
  border: 1px solid var(--border-color);
  border-radius: 14px;
  background: var(--surface-background);
}

.not-found p {
  margin: 0 0 16px;
  color: var(--text-primary);
}

.not-found a {
  color: var(--text-primary);
  text-decoration: underline;
  font-weight: 700;
}

@media (max-width: 600px) {
  .page-heading h2 {
    font-size: 24px;
  }
}
</style>
