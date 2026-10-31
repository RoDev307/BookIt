<template>
  <RouterView />
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
</script>

<style>
*,
*::before,
*::after {
  box-sizing: border-box;
}

html,
body,
#app {
  margin: 0;
  min-height: 100vh;
  width: 100%;
}

body {
  padding: 0;
  background: #f3f4f6;
  font-family: Arial, Helvetica, sans-serif;
}

#app {
  display: block;
  max-width: none;
  padding: 0;
  font-weight: normal;
}
</style>
