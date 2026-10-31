<template>
  <form class="business-form" @submit.prevent="submitForm">
    <fieldset>
      <legend>Datos del comercio</legend>

      <div class="fields-grid">
        <div class="field">
          <label for="business-name">Nombre del comercio *</label>
          <input
            id="business-name"
            v-model="form.business_name"
            type="text"
            maxlength="255"
            required
          />
        </div>

        <div class="field">
          <label for="business-email">Correo del comercio</label>
          <input id="business-email" v-model="form.email" type="email" maxlength="255" />
        </div>

        <div class="field full-width">
          <label for="business-description">Descripción</label>
          <textarea
            id="business-description"
            v-model="form.description"
            rows="4"
            maxlength="1000"
          ></textarea>
        </div>

        <div class="field full-width">
          <label for="business-image">URL de la imagen</label>
          <input
            id="business-image"
            v-model="form.image_url"
            type="url"
            placeholder="https://ejemplo.com/imagen.jpg"
            maxlength="255"
          />
        </div>
      </div>
    </fieldset>

    <fieldset>
      <legend>Administrador del comercio</legend>

      <div class="fields-grid">
        <div class="field">
          <label for="admin-name"> Nombre del administrador {{ isEditing ? '' : '*' }} </label>
          <input
            id="admin-name"
            v-model="form.admin_name"
            type="text"
            autocomplete="name"
            maxlength="255"
            :required="!isEditing"
          />
        </div>

        <div class="field">
          <label for="admin-email"> Correo del administrador {{ isEditing ? '' : '*' }} </label>
          <input
            id="admin-email"
            v-model="form.admin_email"
            type="email"
            autocomplete="username"
            maxlength="255"
            :required="!isEditing"
          />
        </div>

        <div class="field full-width">
          <label for="admin-password">
            {{ isEditing ? 'Nueva contraseña' : 'Contraseña *' }}
          </label>
          <input
            id="admin-password"
            v-model="form.admin_password"
            type="password"
            autocomplete="new-password"
            minlength="8"
            :required="!isEditing"
          />
          <small>
            {{
              isEditing
                ? 'Deja este campo vacío para conservar la contraseña actual.'
                : 'Debe tener al menos 8 caracteres.'
            }}
          </small>
        </div>
      </div>
    </fieldset>

    <p class="form-note">Los campos con * son obligatorios.</p>

    <div class="form-actions">
      <RouterLink to="/superadmin/businesses" class="cancel-button"> Cancelar </RouterLink>

      <button type="submit" class="save-button">
        {{ isEditing ? 'Guardar cambios' : 'Registrar comercio' }}
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import { RouterLink } from 'vue-router'

interface BusinessFormData {
  business_name: string
  email: string
  description: string
  image_url: string
  admin_name: string
  admin_email: string
  admin_password: string
}

const props = withDefaults(
  defineProps<{
    isEditing?: boolean
    initialData?: Partial<BusinessFormData>
  }>(),
  {
    isEditing: false,
  },
)

const emit = defineEmits<{
  submit: [data: BusinessFormData]
}>()

const form = reactive<BusinessFormData>({
  business_name: '',
  email: '',
  description: '',
  image_url: '',
  admin_name: '',
  admin_email: '',
  admin_password: '',
})

watch(
  () => props.initialData,
  (data) => {
    form.business_name = data?.business_name ?? ''
    form.email = data?.email ?? ''
    form.description = data?.description ?? ''
    form.image_url = data?.image_url ?? ''
    form.admin_name = data?.admin_name ?? ''
    form.admin_email = data?.admin_email ?? ''
    form.admin_password = ''
  },
  { immediate: true },
)

function submitForm() {
  emit('submit', { ...form })
}
</script>

<style scoped>
.business-form {
  padding: 28px;
  border: 1px solid var(--border-color);
  border-radius: 18px;
  background: var(--surface-background);
  box-shadow: 0 2px 5px rgb(0 0 0 / 6%);
}

fieldset {
  min-width: 0;
  margin: 0 0 28px;
  padding: 0;
  border: none;
}

legend {
  margin-bottom: 20px;
  padding: 0;
  color: var(--text-primary);
  font-size: 20px;
  font-weight: 700;
}

.fields-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 20px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
}

.full-width {
  grid-column: 1 / -1;
}

label {
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 600;
}

input,
textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid var(--input-border);
  border-radius: 9px;
  background: var(--surface-secondary);
  color: var(--text-primary);
  font: inherit;
}

input::placeholder,
textarea::placeholder {
  color: var(--text-secondary);
  opacity: 1;
}

textarea {
  resize: vertical;
}

input:focus,
textarea:focus {
  border-color: var(--primary-color);
  outline: 2px solid var(--primary-color);
  outline-offset: 2px;
}

small,
.form-note {
  color: var(--text-secondary);
  font-size: 13px;
  line-height: 1.5;
}

.form-note {
  margin: 0 0 20px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.cancel-button,
.save-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 20px;
  border: 1px solid transparent;
  border-radius: 9px;
  text-decoration: none;
  font: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
}

.cancel-button {
  border-color: var(--border-color);
  background: var(--surface-secondary);
  color: var(--text-primary);
}

.cancel-button:hover {
  border-color: var(--primary-color);
}

.save-button {
  background: var(--primary-color);
  color: #ffffff;
}

.save-button:hover {
  background: var(--primary-hover);
}

.cancel-button:focus-visible,
.save-button:focus-visible {
  outline: 3px solid #a5b4fc;
  outline-offset: 3px;
}

@media (max-width: 600px) {
  .business-form {
    padding: 20px;
  }

  .fields-grid {
    grid-template-columns: 1fr;
  }
}
</style>
