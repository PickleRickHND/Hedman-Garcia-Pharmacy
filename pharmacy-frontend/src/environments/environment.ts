/**
 * Configuración de entorno.
 * apiUrl apunta al backend Laravel (php artisan serve → puerto 8000 por defecto).
 * CORS del backend permite el origen del frontend vía FRONTEND_URL (http://localhost:4200).
 */
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
};
