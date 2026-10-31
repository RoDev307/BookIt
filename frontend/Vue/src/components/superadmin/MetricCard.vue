<template>
  <article class="metric-card">
    <div>
      <h3>{{ title }}</h3>
      <p :class="['metric-value', tone]">{{ value }}</p>
    </div>

    <span :class="['metric-icon', tone]" aria-hidden="true">
      {{ icon }}
    </span>
  </article>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    title: string
    value: number
    icon: string
    tone?: 'default' | 'success' | 'danger'
  }>(),
  {
    tone: 'default',
  },
)
</script>

<style scoped>
.metric-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  min-width: 0;
  padding: 24px;
  border: 1px solid var(--border-color);
  border-radius: 18px;
  background: var(--surface-background);
  box-shadow: 0 2px 5px rgb(0 0 0 / 6%);
}

h3 {
  margin: 0 0 12px;
  color: var(--text-secondary);
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.metric-value {
  margin: 0;
  color: var(--text-primary);
  font-size: 36px;
  font-weight: 800;
}

.metric-value.success {
  color: var(--success-text);
}

.metric-value.danger {
  color: var(--danger-text);
}

.metric-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 52px;
  height: 52px;
  border: 1px solid var(--info-border);
  border-radius: 14px;
  background: var(--info-background);
  color: var(--info-text);
  font-size: 25px;
}

.metric-icon.success {
  border-color: var(--success-border);
  background: var(--success-background);
  color: var(--success-text);
}

.metric-icon.danger {
  border-color: var(--danger-border);
  background: var(--danger-background);
  color: var(--danger-text);
}
</style>
