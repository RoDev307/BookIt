import { expect, test } from '@playwright/test';

test('la aplicación sin sesión presenta el acceso administrativo', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveURL(/\/login$/);
  await expect(page.getByRole('heading', { name: 'Iniciar sesión' })).toBeVisible();
});
