import { TestBed } from '@angular/core/testing';
import { provideHttpClient, withXhr } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { environment } from '../../../environments/environment';
import { NotificationService } from './notification.service';

describe('NotificationService', () => {
  let service: NotificationService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [NotificationService, provideHttpClient(withXhr()), provideHttpClientTesting()],
    });
    service = TestBed.inject(NotificationService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => httpMock.verify());

  it('refresh pide GET /notifications y actualiza alerts y count', () => {
    const payload = {
      data: [{ type: 'low_stock', label: 'Stock bajo: 3 productos', count: 3, variant: 'warning' }],
      count: 1,
    };

    service.refresh().subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}/notifications`);
    expect(req.request.method).toBe('GET');
    req.flush(payload);

    expect(service.count()).toBe(1);
    expect(service.alerts().length).toBe(1);
    expect(service.alerts()[0].type).toBe('low_stock');
  });

  it('empieza con estado vacío', () => {
    expect(service.count()).toBe(0);
    expect(service.alerts()).toEqual([]);
  });
});
