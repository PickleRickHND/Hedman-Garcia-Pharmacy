/**
 * Configuración de entorno para pruebas E2E (Playwright).
 * Reemplaza environment.ts vía fileReplacements en la configuración `e2e` de angular.json.
 * Apunta al backend Laravel levantado en el puerto 8001 (el 8000 lo ocupa el proyecto petlab).
 * NO modificar environment.ts de forma persistente: este archivo es el override solo para E2E.
 */
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8001/api',
};
