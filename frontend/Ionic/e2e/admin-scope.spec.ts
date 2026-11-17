import { expect, test, type Page } from '@playwright/test';

const admin = { id: 1, name: 'Administración', email: 'admin@example.test', role: 'admin_business', business_id: 1 };
const client = { ...admin, role: 'client', business_id: null };
async function session(page: Page, user: typeof admin | typeof client = admin) {
  await page.addInitScript((u) => {
    localStorage.setItem('token', 'test-token');
    localStorage.setItem('user', JSON.stringify(u));
  }, user);
  await page.route('**/api/v1/auth/me', route => route.fulfill({ json: { success: true, data: { user } } }));
}
test('el login no ofrece registro de cliente y la ruta antigua vuelve al acceso', async ({ page }) => {
  await page.goto('/cliente');
  await expect(page).toHaveURL(/\/login$/);
  await expect(page.getByRole('heading', { name: 'Iniciar sesión' })).toBeVisible();
  await expect(page.getByText('Crear cuenta', { exact: false })).toHaveCount(0);
});
test('una sesión de cliente no entra al área del negocio', async ({ page }) => {
  await session(page, client);
  await page.goto('/tabs/clientes');
  await expect(page).toHaveURL(/\/login$/);
  expect(await page.evaluate(() => localStorage.getItem('token'))).toBeNull();
});
test('el login de cliente se rechaza y su token se revoca', async ({ page }) => {
  await page.route('**/api/v1/auth/login', route => route.fulfill({ json: { success: true, data: { token: 'client-token', user: client } } }));
  let revoked = false;
  await page.route('**/api/v1/auth/logout', async route => {
    revoked = route.request().headers()['authorization'] === 'Bearer client-token';
    await route.fulfill({ json: { success: true } });
  });
  await page.goto('/login');
  await page.locator('#email').fill('client@example.test');
  await page.locator('#password').fill('password-test');
  await page.getByRole('button', { name: 'Entrar', exact: true }).click();
  await expect(page.getByRole('alert')).toContainText('cuenta administrativa');
  await expect.poll(() => revoked).toBe(true);
  expect(await page.evaluate(() => localStorage.getItem('token'))).toBeNull();
});
test('el administrador conserva ficha historial y agendamiento sin vinculación de cuentas', async ({ page }) => {
  await session(page);
  await page.route('**/api/v1/tenant/clients?**', route => route.fulfill({ json: { data: { data: [{ id: 7, name: 'Pedro Pérez', phone: '70001234', email: null, user_id: 2, appointments_count: 1 }], current_page: 1, last_page: 1, total: 1 } } }));
  await page.route('**/api/v1/tenant/clients/7/appointments?**', route => route.fulfill({ json: { data: { data: [{ id: 9, appointment_time: '2030-01-07 10:00:00', status: 'confirmed', staff_name: 'Profesional', service: { name: 'Consulta' } }], current_page: 1, last_page: 1, total: 1 } } }));
  await page.goto('/tabs/clientes');
  await page.getByRole('button', { name: /Pedro Pérez/ }).click();
  await expect(page.getByRole('heading', { name: 'Historial de citas' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Agendar cita', exact: true })).toHaveAttribute('href', /client_id=7/);
  await expect(page.getByText('Consulta', { exact: true })).toBeVisible();
  await expect(page.getByText('Cuenta de acceso', { exact: true })).toHaveCount(0);
  await expect(page.getByRole('button', { name: /vinculación/i })).toHaveCount(0);
});
