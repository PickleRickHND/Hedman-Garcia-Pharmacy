import { expect, Page, test } from '@playwright/test';
import { DEMO_USER } from './support/demo-user';

/**
 * E2E del flujo de LOGIN del frontend Angular.
 *
 * Selectores (verificados contra login.html / shell.html):
 *  - inputs:  #email, #password
 *  - submit:  button.submit (type=submit dentro del <form>)
 *  - error:   .alert[role=alert]
 *  - éxito:   redirección a /dashboard + .profile__name (shell topbar)
 *
 * Nota headless: el click sobre button[type=submit] no siempre propaga el evento de
 * submit del form. Por eso se dispara con form.requestSubmit() (mismo patrón del proyecto).
 */

const LOGIN_PATH = '/login';

/** Llena email + password y dispara el submit del form de forma robusta en headless. */
async function fillAndSubmit(page: Page, email: string, password: string): Promise<void> {
  await page.fill('#email', email);
  await page.fill('#password', password);
  await page.locator('form').evaluate((f: HTMLFormElement) => f.requestSubmit());
}

test.beforeEach(async ({ page }) => {
  // Sesión limpia: sin token/usuario en localStorage para que guestGuard deje ver /login.
  await page.goto(LOGIN_PATH);
  await page.evaluate(() => localStorage.clear());
  await page.goto(LOGIN_PATH);
  await expect(page.locator('form')).toBeVisible();
});

test('login válido redirige a /dashboard y muestra el usuario', async ({ page }) => {
  await fillAndSubmit(page, DEMO_USER.email, DEMO_USER.password);

  // Redirección al dashboard tras autenticar.
  await expect(page).toHaveURL(/\/dashboard$/);

  // El shell (topbar) muestra el nombre y el avatar/iniciales del usuario.
  await expect(page.locator('.profile__name')).toHaveText(DEMO_USER.name);
  await expect(page.locator('.profile__avatar')).toBeVisible();

  // El token quedó persistido en localStorage bajo la clave 'hg-token'.
  const token = await page.evaluate(() => localStorage.getItem('hg-token'));
  expect(token).toBeTruthy();
});

test('credenciales inválidas muestran la alerta de error y permanecen en /login', async ({
  page,
}) => {
  await fillAndSubmit(page, DEMO_USER.email, 'PasswordIncorrecta!');

  // Aparece la alerta de error y seguimos en /login.
  const alert = page.locator('.alert[role="alert"]');
  await expect(alert).toBeVisible();
  await expect(alert).not.toBeEmpty();
  await expect(page).toHaveURL(/\/login$/);

  // No se persistió sesión.
  const token = await page.evaluate(() => localStorage.getItem('hg-token'));
  expect(token).toBeNull();
});

test('submit con campos vacíos no navega (validación bloquea el envío)', async ({ page }) => {
  // Disparamos el submit sin llenar nada.
  await page.locator('form').evaluate((f: HTMLFormElement) => f.requestSubmit());

  // Seguimos en /login: la validación del form (required/email) bloquea la navegación.
  await expect(page).toHaveURL(/\/login$/);
  await expect(page.locator('form')).toBeVisible();

  // No se generó alerta de error de API (el guard de validación corta antes del request).
  await expect(page.locator('.alert[role="alert"]')).toHaveCount(0);
});
