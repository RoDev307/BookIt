# Vue

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VS Code](https://code.visualstudio.com/) + [Vue (Official)](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Recommended Browser Setup

- Chromium-based browsers (Chrome, Edge, Brave, etc.):
  - [Vue.js devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd)
  - [Turn on Custom Object Formatter in Chrome DevTools](http://bit.ly/object-formatters)
- Firefox:
  - [Vue.js devtools](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)
  - [Turn on Custom Object Formatter in Firefox DevTools](https://fxdx.dev/firefox-devtools-custom-object-formatters/)

## Type Support for `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) to make the TypeScript language service aware of `.vue` types.

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
pnpm install
```

### Compile and Hot-Reload for Development

```sh
pnpm dev
```

### Type-Check, Compile and Minify for Production

```sh
pnpm build
```

### Lint with [ESLint](https://eslint.org/)

```sh
pnpm lint
```

## Panel de superadministrador

Vistas implementadas en Vue con TypeScript:

- Inicio de sesión.
- Dashboard de métricas y últimas reservas.
- Listado de comercios.
- Registro de comercio y administrador.
- Edición de comercio y administrador.
- Tema claro y oscuro con preferencia guardada.
- Diseño adaptable a pantallas pequeñas.

### Estado de la integración

Las vistas utilizan datos temporales definidos en el frontend.

Pendiente de integración con la API de Laravel:

- Autenticación y cierre de sesión.
- Protección de rutas y validación del rol super_admin.
- Consulta de métricas y comercios.
- Registro y actualización de comercios.
- Manejo de errores del backend.

El login todavía no autentica y los formularios no guardan datos.
El enlace de vista previa permite navegar por el panel sin autenticación.

El dashboard actual muestra una tabla de reservas de ejemplo.
La API revisada de /superadmin/dashboard devuelve métricas y
recent_businesses; falta acordar cómo obtener las últimas reservas.

Al integrar el formulario, POST /superadmin/businesses espera
business_name y PUT /superadmin/businesses/{id} espera name.
