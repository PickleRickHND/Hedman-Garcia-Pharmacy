import { TestBed } from '@angular/core/testing';
import { provideHttpClient, withXhr } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { environment } from '../../../environments/environment';
import { AuthService } from './auth.service';

describe('AuthService — recuperación de contraseña', () => {
  let service: AuthService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    localStorage.clear();
    TestBed.configureTestingModule({
      providers: [AuthService, provideHttpClient(withXhr()), provideHttpClientTesting()],
    });
    service = TestBed.inject(AuthService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => httpMock.verify());

  it('forgotPassword hace POST /forgot-password con el correo', () => {
    service.forgotPassword('ana@pharmacy.hn').subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}/forgot-password`);
    expect(req.request.method).toBe('POST');
    expect(req.request.body).toEqual({ email: 'ana@pharmacy.hn' });
    req.flush({ message: 'ok' });
  });

  it('resetPassword hace POST /reset-password con código y contraseña', () => {
    const payload = {
      email: 'ana@pharmacy.hn',
      code: '123456',
      password: 'nuevaClave123',
      password_confirmation: 'nuevaClave123',
    };

    service.resetPassword(payload).subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}/reset-password`);
    expect(req.request.method).toBe('POST');
    expect(req.request.body).toEqual(payload);
    req.flush({ message: 'ok' });
  });
});
