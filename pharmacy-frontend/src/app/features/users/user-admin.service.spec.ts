import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { UserPayload } from '../../core/models/user.model';
import { UserAdminService } from './user-admin.service';

describe('UserAdminService', () => {
  let service: UserAdminService;
  let httpMock: HttpTestingController;
  const base = environment.apiUrl;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [UserAdminService, provideHttpClient(), provideHttpClientTesting()],
    });
    service = TestBed.inject(UserAdminService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => httpMock.verify());

  it('lista usuarios con filtros search/role y paginación', () => {
    service.list({ search: 'ana', role: 'Cajero', page: 2 }).subscribe();

    const req = httpMock.expectOne(
      (r) =>
        r.url === `${base}/users` &&
        r.params.get('search') === 'ana' &&
        r.params.get('role') === 'Cajero' &&
        r.params.get('page') === '2',
    );
    expect(req.request.method).toBe('GET');
    req.flush({ data: [], links: {}, meta: {} });
  });

  it('omite search/role vacíos en los params', () => {
    service.list({}).subscribe();

    const req = httpMock.expectOne((r) => r.url === `${base}/users`);
    expect(req.request.params.has('search')).toBeFalse();
    expect(req.request.params.has('role')).toBeFalse();
    expect(req.request.params.get('per_page')).toBe('12');
    req.flush({ data: [], links: {}, meta: {} });
  });

  it('crea un usuario (POST)', () => {
    const payload: UserPayload = {
      name: 'Nuevo',
      email: 'nuevo@pharmacy.hn',
      role: 'Cajero',
      password: 'Password123!',
      password_confirmation: 'Password123!',
    };
    service.create(payload).subscribe();

    const req = httpMock.expectOne(`${base}/users`);
    expect(req.request.method).toBe('POST');
    expect(req.request.body).toEqual(payload);
    req.flush({ data: {} });
  });

  it('actualiza un usuario (PUT)', () => {
    service.update(7, { name: 'X', email: 'x@x.hn', role: 'Administrador' }).subscribe();

    const req = httpMock.expectOne(`${base}/users/7`);
    expect(req.request.method).toBe('PUT');
    req.flush({ data: {} });
  });

  it('elimina un usuario (DELETE)', () => {
    service.remove(9).subscribe();

    const req = httpMock.expectOne(`${base}/users/9`);
    expect(req.request.method).toBe('DELETE');
    req.flush(null);
  });

  it('obtiene los roles', () => {
    service.roles().subscribe((res) => expect(res.data).toEqual(['Administrador', 'Cajero']));

    const req = httpMock.expectOne(`${base}/roles`);
    req.flush({ data: ['Administrador', 'Cajero'] });
  });
});
