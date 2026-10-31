<template>
  <section>
    <header class="page-heading">
      <h2>Consola de Infraestructura SaaS</h2>
      <p>Monitoreo de actividad y estadísticas de todos los comercios.</p>
    </header>

    <div class="metrics-grid">
      <MetricCard title="Comercios afiliados" :value="5" icon="🌐" />

      <MetricCard title="Reservas globales" :value="14" icon="📅" />

      <MetricCard title="Citas confirmadas" :value="7" icon="✓" tone="success" />

      <MetricCard title="Citas canceladas" :value="5" icon="✕" tone="danger" />
    </div>

    <div class="reservations-panel">
      <header class="table-heading">
        <h3>Últimas reservas en la red</h3>
        <p>Listado de reservas de todos los comercios.</p>
      </header>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Establecimiento</th>
              <th scope="col">Cliente</th>
              <th scope="col">Fecha / hora</th>
              <th scope="col">Estado</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="reservation in reservations" :key="reservation.id">
              <td>#{{ reservation.id }}</td>
              <td class="business-name">{{ reservation.business }}</td>
              <td>{{ reservation.client }}</td>
              <td>{{ reservation.date }} — {{ reservation.time }}</td>
              <td>
                <span :class="['status', reservation.status]">
                  {{ reservation.status === 'confirmed' ? 'Confirmada' : 'Cancelada' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import MetricCard from '../../components/superadmin/MetricCard.vue'

interface Reservation {
  id: number
  business: string
  client: string
  date: string
  time: string
  status: 'confirmed' | 'cancelled'
}

const reservations: Reservation[] = [
  {
    id: 15,
    business: 'Clínica Dental Sonrisas',
    client: 'Cliente de ejemplo 1',
    date: '26/10/2026',
    time: '10:00 AM',
    status: 'cancelled',
  },
  {
    id: 14,
    business: 'Clínica Dental Sonrisas',
    client: 'Cliente de ejemplo 2',
    date: '26/10/2026',
    time: '2:00 PM',
    status: 'confirmed',
  },
  {
    id: 8,
    business: 'Barbería de x',
    client: 'Cliente de ejemplo 3',
    date: '25/10/2026',
    time: '1:00 PM',
    status: 'confirmed',
  },
]
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

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 20px;
}

.demo-note {
  margin-top: 16px;
  color: var(--text-secondary);
  font-size: 13px;
}

.reservations-panel {
  margin-top: 30px;
  overflow: hidden;
  border: 1px solid var(--border-color);
  border-radius: 18px;
  background: var(--surface-background);
  box-shadow: 0 2px 5px rgb(0 0 0 / 6%);
}

.table-heading {
  padding: 24px;
  border-bottom: 1px solid var(--border-color);
}

.table-heading h3 {
  margin: 0 0 8px;
  color: var(--text-primary);
  font-size: 20px;
}

.table-heading p {
  margin: 0;
  color: var(--text-secondary);
  font-size: 14px;
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

.status {
  display: inline-block;
  padding: 5px 10px;
  border: 1px solid;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.status.confirmed {
  border-color: var(--success-border);
  background: var(--success-background);
  color: var(--success-text);
}

.status.cancelled {
  border-color: var(--danger-border);
  background: var(--danger-background);
  color: var(--danger-text);
}

@media (max-width: 1200px) {
  .metrics-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 600px) {
  .metrics-grid {
    grid-template-columns: 1fr;
  }

  .page-heading h2 {
    font-size: 24px;
  }
}
</style>
