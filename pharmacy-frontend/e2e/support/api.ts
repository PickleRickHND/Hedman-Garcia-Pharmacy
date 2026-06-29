import { APIRequestContext, Page, request } from '@playwright/test';
import { DEMO_USER } from './demo-user';

/**
 * Helpers de API para los flujos operacionales E2E.
 *
 * Filosofía: la UI se reserva para la ACCIÓN BAJO PRUEBA; los prerequisitos de estado
 * (login, productos, caja, factura) se montan por API (rápido y determinístico). Esto evita
 * flakiness y mantiene cada spec enfocado en su flujo.
 *
 * El backend corre en :8001 (el 8000 lo ocupa petlab). Mismo origen que environment.e2e.ts.
 *
 * IMPORTANTE (resolución de URL): el baseURL lleva slash FINAL y los paths van SIN slash
 * inicial. Con `new URL(path, base)`, un path con slash inicial ('/login') descarta el '/api'
 * del base y pega contra rutas web → 405/500. Con base 'http://localhost:8001/api/' + path
 * relativo 'login' resuelve correctamente a 'http://localhost:8001/api/login'.
 */
export const API_BASE = 'http://localhost:8001/api/';

/** Crea un contexto de API autenticado como el Administrador demo (token Bearer Sanctum). */
export async function apiLogin(): Promise<{ ctx: APIRequestContext; token: string; userId: number }> {
  const anon = await request.newContext({ baseURL: API_BASE });
  const res = await anon.post('login', {
    data: { email: DEMO_USER.email, password: DEMO_USER.password },
    headers: { Accept: 'application/json' },
  });
  if (!res.ok()) {
    throw new Error(`apiLogin falló (${res.status()}): ${await res.text()}`);
  }
  const body = (await res.json()) as { token: string; user: { id: number } };
  await anon.dispose();

  const ctx = await request.newContext({
    baseURL: API_BASE,
    extraHTTPHeaders: { Authorization: `Bearer ${body.token}`, Accept: 'application/json' },
  });
  return { ctx, token: body.token, userId: body.user.id };
}

/**
 * Siembra token + usuario en localStorage (claves hg-token / hg-user) para que la SPA
 * arranque autenticada sin pasar por el formulario de login (ese flujo lo cubre login.spec.ts).
 */
export async function seedSession(page: Page, token: string): Promise<void> {
  // Necesitamos el objeto `user` que la app guarda; lo traemos de /me con el token.
  const me = await request.newContext({
    baseURL: API_BASE,
    extraHTTPHeaders: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
  });
  const res = await me.get('me');
  const user = ((await res.json()) as { data: unknown }).data;
  await me.dispose();

  // Cargar el origin antes de escribir en localStorage.
  await page.goto('/login');
  await page.evaluate(
    ({ t, u }) => {
      localStorage.setItem('hg-token', t);
      localStorage.setItem('hg-user', JSON.stringify(u));
    },
    { t: token, u: user },
  );
}

/** Devuelve el id del producto con el SKU dado (debe existir; lo siembra globalSetup). */
export async function productIdBySku(ctx: APIRequestContext, sku: string): Promise<number> {
  const res = await ctx.get(`products?search=${encodeURIComponent(sku)}&per_page=10`);
  const body = (await res.json()) as { data: { id: number; sku: string }[] };
  const found = body.data.find((p) => p.sku === sku);
  if (!found) throw new Error(`No se encontró el producto E2E con SKU ${sku}.`);
  return found.id;
}

/** id del primer método de pago activo (el POS preselecciona el primero). */
export async function firstPaymentMethodId(ctx: APIRequestContext): Promise<number> {
  const res = await ctx.get('payment-methods');
  const body = (await res.json()) as { data: { id: number; name: string }[] };
  if (!body.data.length) throw new Error('No hay métodos de pago activos.');
  return body.data[0].id;
}

/** id del método de pago "Efectivo" (para que el arqueo de caja sume el total a lo esperado). */
export async function cashPaymentMethodId(ctx: APIRequestContext): Promise<number> {
  const res = await ctx.get('payment-methods');
  const body = (await res.json()) as { data: { id: number; name: string }[] };
  const cash = body.data.find((p) => p.name === 'Efectivo') ?? body.data[0];
  return cash.id;
}

/**
 * Garantiza que NO haya ninguna caja abierta (la restricción del backend es global: solo
 * puede existir una caja abierta a la vez, sin importar el usuario). Si encuentra una abierta
 * la cierra por API. Hace cada spec auto-sanable e independiente del orden de ejecución.
 */
export async function ensureNoOpenRegister(ctx: APIRequestContext): Promise<void> {
  const res = await ctx.get('cash-registers/current');
  const body = (await res.json()) as { data: { id: number } | null };
  if (body.data) {
    await ctx.post(`cash-registers/${body.data.id}/close`, {
      data: { actual_amount: 0, notes: 'Cierre automático E2E (limpieza de estado).' },
    });
  }
}

/** Abre una caja por API y devuelve su id. Úsalo como prerequisito en specs que NO prueban caja. */
export async function openRegister(ctx: APIRequestContext, openingAmount = 1000): Promise<number> {
  await ensureNoOpenRegister(ctx);
  const res = await ctx.post('cash-registers/open', { data: { opening_amount: openingAmount } });
  if (!res.ok()) throw new Error(`openRegister falló (${res.status()}): ${await res.text()}`);
  const body = (await res.json()) as { data: { id: number } };
  return body.data.id;
}

/** Crea una factura por API (prerequisito para el flujo de devoluciones). Devuelve la factura. */
export async function createInvoice(
  ctx: APIRequestContext,
  payload: {
    customer_name: string;
    payment_method_id: number;
    items: { product_id: number; quantity: number; discount_percent?: number }[];
  },
): Promise<{ id: number; invoice_number: string; items: { id: number; product_id: number }[] }> {
  const res = await ctx.post('invoices', { data: payload });
  if (!res.ok()) throw new Error(`createInvoice falló (${res.status()}): ${await res.text()}`);
  const body = (await res.json()) as {
    data: { id: number; invoice_number: string; items: { id: number; product_id: number }[] };
  };
  return body.data;
}
