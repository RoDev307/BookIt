<template>
  <section>
    <header class="page-heading">
      <div>
        <h2>Panel Maestro: Control de negocios</h2>
        <p>Monitoreo, edición y control global de los comercios de BookIt.</p>
      </div>

      <RouterLink to="/superadmin/businesses/create" class="create-button">
        + Registrar nuevo negocio
      </RouterLink>
    </header>

    <div class="businesses-panel">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Nombre del comercio</th>
              <th scope="col">Slug en URL</th>
              <th scope="col" class="actions-column">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="business in businesses" :key="business.id">
              <td>#{{ business.id }}</td>
              <td class="business-name">{{ business.name }}</td>
              <td class="business-slug">{{ business.slug }}</td>
              <td class="actions-column">
                <RouterLink
                  :to="`/superadmin/businesses/${business.id}/edit`"
                  class="edit-button"
                  :aria-label="`Modificar ${business.name}`"
                >
                  ⚙ Modificar
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'

interface Business {
  id: number
  name: string
  slug: string
}

const businesses: Business[] = [
  {
    id: 1,
    name: 'Barbería de x',
    slug: 'barberia-de-x',
  },
  {
    id: 2,
    name: 'Clínica Dental Sonrisas',
    slug: 'clinica-dental-sonrisas',
  },
  {
    id: 3,
    name: 'Taller AutoFix',
    slug: 'taller-autofix',
  },
  {
    id: 4,
    name: 'Hotel Pariston',
    slug: 'hotel-pariston',
  },
  {
    id: 5,
    name: 'Restaurante Premium',
    slug: 'restaurante-premium',
  },
]
</script>

<style scoped>
.page-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 24px;
  margin-bottom: 20px;
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

.create-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 14px 20px;
  border-radius: 12px;
  background: var(--primary-color);
  color: #ffffff;
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
}

.create-button:hover {
  background: var(--primary-hover);
}

.demo-note {
  margin: 0 0 20px;
  color: var(--text-secondary);
  font-size: 13px;
}

.businesses-panel {
  overflow: hidden;
  border: 1px solid var(--border-color);
  border-radius: 18px;
  background: var(--surface-background);
  box-shadow: 0 2px 5px rgb(0 0 0 / 6%);
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

th,
td {
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
  white-space: nowrap;
}

th {
  background: var(--surface-secondary);
  color: var(--text-secondary);
  font-size: 12px;
  letter-spacing: 1px;
  text-transform: uppercase;
}

td {
  color: var(--text-primary);
  font-size: 14px;
}

tbody tr:last-child td {
  border-bottom: none;
}

.business-name {
  font-weight: 700;
}

.business-slug {
  font-family: monospace;
}

.actions-column {
  text-align: right;
}

.edit-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 9px 14px;
  border: 1px solid var(--border-color);
  border-radius: 9px;
  background: var(--surface-secondary);
  color: var(--text-primary);
  text-decoration: none;
  font-size: 13px;
  font-weight: 700;
}

.edit-button:hover {
  background: var(--primary-color);
  color: #ffffff;
}

.create-button:focus-visible,
.edit-button:focus-visible {
  outline: 3px solid #a5b4fc;
  outline-offset: 3px;
}

@media (max-width: 600px) {
  .page-heading h2 {
    font-size: 24px;
  }

  .create-button {
    width: 100%;
  }
}
</style>
