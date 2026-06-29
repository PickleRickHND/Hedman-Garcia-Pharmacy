import { signal } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { Router } from '@angular/router';
import { of } from 'rxjs';
import { Alert } from '../../core/models/notification.model';
import { NotificationsBell } from './notifications-bell';
import { NotificationService } from './notification.service';

describe('NotificationsBell', () => {
  let fixture: ComponentFixture<NotificationsBell>;
  let cmp: NotificationsBell;
  let router: jasmine.SpyObj<Router>;
  const alerts = signal<Alert[]>([]);
  const count = signal(0);

  beforeEach(() => {
    alerts.set([]);
    count.set(0);
    router = jasmine.createSpyObj<Router>('Router', ['navigate']);

    TestBed.configureTestingModule({
      imports: [NotificationsBell],
      providers: [
        {
          provide: NotificationService,
          useValue: {
            alerts,
            count,
            refresh: () => of({ data: [], count: 0 }),
          },
        },
        { provide: Router, useValue: router },
      ],
    });
    fixture = TestBed.createComponent(NotificationsBell);
    cmp = fixture.componentInstance;
    fixture.detectChanges();
  });

  function html(): HTMLElement {
    return fixture.nativeElement as HTMLElement;
  }

  it('muestra el badge con el conteo cuando hay alertas', () => {
    count.set(4);
    fixture.detectChanges();
    expect(html().querySelector('.nb__badge')?.textContent?.trim()).toBe('4');
  });

  it('oculta el badge cuando no hay alertas', () => {
    count.set(0);
    fixture.detectChanges();
    expect(html().querySelector('.nb__badge')).toBeNull();
  });

  it('lista las alertas al abrir el panel', () => {
    alerts.set([
      { type: 'low_stock', label: 'Stock bajo: 2 productos', count: 2, variant: 'warning' },
      { type: 'cash_closed', label: 'No hay una caja abierta', count: 0, variant: 'info' },
    ]);
    cmp.toggle();
    fixture.detectChanges();
    expect(html().querySelectorAll('.nb__item').length).toBe(2);
    expect(html().textContent).toContain('No hay una caja abierta');
  });

  it('muestra el estado vacío sin alertas', () => {
    cmp.toggle();
    fixture.detectChanges();
    expect(html().querySelector('.nb__empty')).not.toBeNull();
  });

  it('navega al catálogo filtrado según el tipo de alerta', () => {
    cmp.go({ type: 'expired', label: 'Vencidos: 1 producto', count: 1, variant: 'danger' });
    expect(router.navigate).toHaveBeenCalledWith(['/products'], { queryParams: { expired: 1 } });
    expect(cmp.open()).toBeFalse();
  });

  it('navega a caja cuando la alerta es cash_closed', () => {
    cmp.go({ type: 'cash_closed', label: 'No hay una caja abierta', count: 0, variant: 'info' });
    expect(router.navigate).toHaveBeenCalledWith(['/cash-registers']);
  });

  it('al accionar una alerta navega y cierra el panel (sin ocultarla del estado)', () => {
    alerts.set([{ type: 'low_stock', label: 'Stock bajo: 2 productos', count: 2, variant: 'warning' }]);
    count.set(1);
    cmp.toggle();
    cmp.go({ type: 'low_stock', label: 'Stock bajo: 2 productos', count: 2, variant: 'warning' });
    expect(router.navigate).toHaveBeenCalledWith(['/products'], { queryParams: { low_stock: 1 } });
    expect(cmp.open()).toBeFalse();
    // La alerta sigue vigente: el componente no la descarta, solo se quita
    // cuando el backend deja de reportarla en el siguiente refresco.
    expect(alerts().length).toBe(1);
    expect(count()).toBe(1);
  });

  it('abrir y cerrar el panel no altera el conteo de alertas', () => {
    count.set(3);
    cmp.toggle(); // abre
    cmp.toggle(); // cierra
    expect(cmp.open()).toBeFalse();
    expect(cmp['service'].count()).toBe(3);
  });
});
