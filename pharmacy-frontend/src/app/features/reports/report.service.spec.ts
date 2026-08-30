import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient, withXhr } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { ReportService } from './report.service';

describe('ReportService', () => {
  let service: ReportService;
  let httpMock: HttpTestingController;
  const base = environment.apiUrl;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ReportService, provideHttpClient(withXhr()), provideHttpClientTesting()],
    });
    service = TestBed.inject(ReportService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => httpMock.verify());

  it('pide ventas con rango from/to', () => {
    service.sales({ from: '2026-06-01', to: '2026-06-30' }).subscribe();

    const req = httpMock.expectOne((r) => r.url === `${base}/reports/sales`);
    expect(req.request.params.get('from')).toBe('2026-06-01');
    expect(req.request.params.get('to')).toBe('2026-06-30');
    req.flush({ data: {} });
  });

  it('pide top productos con limit y sort_by', () => {
    service.products({ from: '2026-06-01', to: '2026-06-30', limit: 20, sort_by: 'revenue' }).subscribe();

    const req = httpMock.expectOne((r) => r.url === `${base}/reports/products`);
    expect(req.request.params.get('limit')).toBe('20');
    expect(req.request.params.get('sort_by')).toBe('revenue');
    req.flush({ data: [] });
  });

  it('pide el snapshot de inventario', () => {
    service.inventory().subscribe();

    const req = httpMock.expectOne(`${base}/reports/inventory`);
    expect(req.request.method).toBe('GET');
    req.flush({ data: {} });
  });
});
