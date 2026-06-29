import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { StockMovementService } from './stock-movement.service';

describe('StockMovementService', () => {
  let service: StockMovementService;
  let httpMock: HttpTestingController;
  const base = environment.apiUrl;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [StockMovementService, provideHttpClient(), provideHttpClientTesting()],
    });
    service = TestBed.inject(StockMovementService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => httpMock.verify());

  it('envía todos los filtros (producto, tipo, rango de fechas)', () => {
    service.list({ product_id: 5, type: 'sale', date_from: '2026-06-01', date_to: '2026-06-30', page: 3 }).subscribe();

    const req = httpMock.expectOne((r) => r.url === `${base}/stock-movements`);
    expect(req.request.params.get('product_id')).toBe('5');
    expect(req.request.params.get('type')).toBe('sale');
    expect(req.request.params.get('date_from')).toBe('2026-06-01');
    expect(req.request.params.get('date_to')).toBe('2026-06-30');
    expect(req.request.params.get('page')).toBe('3');
    req.flush({ data: [], links: {}, meta: {} });
  });

  it('omite product_id nulo y usa per_page por defecto', () => {
    service.list({ product_id: null }).subscribe();

    const req = httpMock.expectOne((r) => r.url === `${base}/stock-movements`);
    expect(req.request.params.has('product_id')).toBeFalse();
    expect(req.request.params.get('per_page')).toBe('20');
    req.flush({ data: [], links: {}, meta: {} });
  });
});
