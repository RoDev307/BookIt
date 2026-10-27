# 🚀 BookIt SaaS - Documentación de la API RESTful (v1)

Esta API está diseñada para ser consumida tanto por la aplicación móvil (**Ionic**) como por la aplicación web (**Vue 3**).

- **URL Base:** `http://localhost:8000/api/v1` (o tu virtual host configurado en XAMPP / producción).
- **Formato de datos:** `JSON` en todas las peticiones y respuestas.
- **Header recomendado en peticiones:**
  ```http
  Accept: application/json
  Content-Type: application/json
  ```
- **Autenticación:** Laravel Sanctum mediante Bearer Token:
  ```http
  Authorization: Bearer <TU_TOKEN_SANCTUM>
  ```

---

## 📦 Estructura Estándar de Respuesta

### Éxito (`200 OK` / `201 Created`):
```json
{
  "success": true,
  "message": "Operación exitosa (opcional)",
  "data": { ... }
}
```

### Error de Validación (`422 Unprocessable Entity`):
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "campo": ["Descripción del error"]
  }
}
```

---

## 🔐 1. Autenticación (`/api/v1/auth`)

| Método | Endpoint | Autenticación | Descripción |
|---|---|---|---|
| `POST` | `/auth/register` | Pública | Registro de nuevo cliente |
| `POST` | `/auth/login` | Pública | Inicio de sesión (obtiene Bearer Token) |
| `GET` | `/auth/me` | Requerida | Datos del usuario autenticado + negocio asignado |
| `PUT` | `/auth/profile` | Requerida | Actualizar datos del perfil |
| `POST` | `/auth/logout` | Requerida | Cierra sesión y revoca el token |

### `POST /auth/register`
**Body:**
```json
{
  "name": "Juan Perez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### `POST /auth/login`
**Body:**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```
**Respuesta:**
```json
{
  "success": true,
  "message": "Inicio de sesión exitoso.",
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Perez",
      "email": "juan@example.com",
      "role": "client", // "client" | "admin_business" | "super_admin"
      "business_id": null
    },
    "token": "1|abcdef123456...",
    "token_type": "Bearer"
  }
}
```

---

## 🏢 2. Catálogo Público & Disponibilidad (`/api/v1/businesses`)

| Método | Endpoint | Autenticación | Descripción |
|---|---|---|---|
| `GET` | `/businesses` | Pública | Listar comercios (soporta `?search=texto`) |
| `GET` | `/businesses/{slug}` | Pública | Detalle de un comercio, sus servicios y horarios |
| `GET` | `/businesses/{slug}/services` | Pública | Servicios activos del comercio |
| `GET` | `/businesses/{slug}/hours` | Pública | Horarios semanales de atención |
| `GET` | `/businesses/{slug}/availability?date=YYYY-MM-DD` | Pública | Horarios y slots disponibles para una fecha |

### `GET /businesses/{slug}/availability?date=2026-11-02`
Permite a Ionic y Vue renderizar directamente la cuadrícula de botones de horarios libres:
```json
{
  "success": true,
  "data": {
    "date": "2026-11-02",
    "is_open": true,
    "available_slots": [
      { "time": "08:00", "available": true, "reason": "Disponible" },
      { "time": "09:00", "available": false, "reason": "Horario ocupado" },
      { "time": "10:00", "available": true, "reason": "Disponible" }
    ]
  }
}
```

---

## 📅 3. Citas de Clientes (`/api/v1`)

| Método | Endpoint | Autenticación | Descripción |
|---|---|---|---|
| `GET` | `/my-appointments` | Requerida | Historial de citas del usuario autenticado |
| `POST` | `/appointments` | Requerida | Reservar una nueva cita con validaciones SaaS |
| `GET` | `/appointments/{id}` | Requerida | Ver detalle de una cita propia |
| `PATCH` | `/appointments/{id}/cancel` | Requerida | Cancelar una cita propia |

### `POST /appointments`
**Body:**
```json
{
  "service_id": 1,
  "appointment_time": "2026-11-02 10:00:00",
  "staff_name": "Asignado por Recepción", // Opcional
  "notes": "Preferencias de la cita" // Opcional
}
```
*(También acepta `fecha_cita: "2026-11-02"` y `hora_cita: "10:00"` en lugar de `appointment_time`).*

---

## 🏪 4. Panel del Comercio Inquilino (`/api/v1/tenant`)
> **Requiere:** Token Bearer con rol `admin_business` o `super_admin`.

### Dashboard & Perfil
| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/tenant/dashboard` | Métricas operativas (citas totales, confirmadas, hoy, etc.) |
| `GET` | `/tenant/business` | Obtener datos de su propio negocio y horarios |
| `PUT` | `/tenant/business` | Actualizar nombre, descripción o imagen |
| `PUT` | `/tenant/business/hours` | Configurar horarios semanales de atención |

### Servicios (CRUD)
| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/tenant/services` | Listar servicios propios |
| `POST` | `/tenant/services` | Crear nuevo servicio |
| `GET` | `/tenant/services/{id}` | Consultar servicio |
| `PUT` | `/tenant/services/{id}` | Actualizar servicio |
| `DELETE` | `/tenant/services/{id}` | Eliminar servicio |

### Citas & Calendario
| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/tenant/appointments` | Listar citas con filtros (`?date=`, `?status=`, etc.) |
| `GET` | `/tenant/appointments/calendar` | Formato para calendarios FullCalendar / Ionic / Vue |
| `POST` | `/tenant/appointments` | Agendamiento manual por recepcionista para cliente externo |
| `PUT` | `/tenant/appointments/{id}` | Modificar cita |
| `PATCH` | `/tenant/appointments/{id}/status` | Cambiar estado (`confirmed`, `completed`, `cancelled`) |
| `GET` | `/tenant/appointments/{id}/pdf` | Descarga de comprobante de cita en PDF |
| `GET` | `/tenant/appointments/report/pdf?periodo=mes` | Descarga de reporte PDF semanal o mensual |

---

## 👑 5. Consola Maestra Global (`/api/v1/superadmin`)
> **Requiere:** Token Bearer con rol `super_admin`.

| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/superadmin/dashboard` | Métricas globales de la plataforma SaaS |
| `GET` | `/superadmin/businesses` | Listar todos los comercios del sistema |
| `POST` | `/superadmin/businesses` | Dar de alta un nuevo negocio y su administrador inicial |
| `GET` | `/superadmin/businesses/{id}` | Detalle completo de un negocio |
| `PUT` | `/superadmin/businesses/{id}` | Actualizar negocio |
| `DELETE` | `/superadmin/businesses/{id}` | Eliminar negocio |
