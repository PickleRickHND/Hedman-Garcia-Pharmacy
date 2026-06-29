import {
  ChangeDetectionStrategy,
  Component,
  DestroyRef,
  ElementRef,
  OnDestroy,
  OnInit,
  inject,
  signal,
} from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import { interval } from 'rxjs';
import { Alert } from '../../core/models/notification.model';
import { Icon } from '../icon/icon';
import { NotificationService } from './notification.service';

const REFRESH_MS = 5 * 60 * 1000;

/** Campana del topbar: badge con el número de alertas y panel desplegable. */
@Component({
  selector: 'app-notifications-bell',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon],
  template: `
    <div class="nb">
      <button
        type="button"
        class="nb__trigger"
        aria-label="Alertas"
        [attr.aria-expanded]="open()"
        (click)="toggle()"
      >
        <app-icon name="bell" />
        @if (service.count() > 0) {
          <span class="nb__badge" aria-hidden="true">{{ service.count() }}</span>
        }
      </button>

      @if (open()) {
        <div class="nb__panel" role="dialog" aria-label="Notificaciones">
          <div class="nb__head">Notificaciones</div>
          @for (alert of service.alerts(); track alert.type) {
            <button type="button" class="nb__item" (click)="go(alert)">
              <span class="nb__dot nb__dot--{{ alert.variant }}" aria-hidden="true">
                <app-icon [name]="iconFor(alert.type)" />
              </span>
              <span class="nb__label">{{ alert.label }}</span>
            </button>
          } @empty {
            <div class="nb__empty">Sin alertas por ahora.</div>
          }
        </div>
      }
    </div>
  `,
  styles: `
    :host {
      display: inline-flex;
      position: relative;
    }
    .nb {
      position: relative;
    }
    /* Réplica de .icon-btn del shell (la clase del shell no cruza la encapsulación). */
    .nb__trigger {
      position: relative;
      display: grid;
      place-items: center;
      width: 38px;
      height: 38px;
      border-radius: 10px;
      border: 1px solid transparent;
      background: transparent;
      color: var(--ink-soft);
      cursor: pointer;
      --icon-size: 19px;
      transition: background 0.12s ease;
    }
    .nb__trigger:hover {
      background: var(--surface-2);
    }
    .nb__badge {
      position: absolute;
      top: -2px;
      right: -2px;
      min-width: 16px;
      height: 16px;
      padding: 0 4px;
      display: grid;
      place-items: center;
      border-radius: 999px;
      background: var(--danger);
      color: #fff;
      font-size: 0.62rem;
      font-weight: 700;
      line-height: 1;
      border: 2px solid var(--surface);
      box-sizing: content-box;
    }
    .nb__panel {
      position: absolute;
      z-index: 70;
      top: calc(100% + 8px);
      right: 0;
      width: 280px;
      padding: 6px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
    }
    .nb__head {
      padding: 8px 10px;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .nb__item {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      padding: 9px 10px;
      border: none;
      background: transparent;
      border-radius: var(--radius-sm);
      text-align: left;
      cursor: pointer;
    }
    .nb__item:hover {
      background: var(--surface-2);
    }
    .nb__dot {
      display: grid;
      place-items: center;
      width: 30px;
      height: 30px;
      border-radius: 8px;
      flex-shrink: 0;
      --icon-size: 16px;
    }
    .nb__dot--warning {
      background: var(--warning-bg);
      color: var(--warning);
    }
    .nb__dot--danger {
      background: var(--danger-bg);
      color: var(--danger);
    }
    .nb__dot--info {
      background: var(--info-bg);
      color: var(--info);
    }
    .nb__label {
      font-size: 0.88rem;
      color: var(--ink);
    }
    .nb__empty {
      padding: 16px 10px;
      text-align: center;
      color: var(--muted);
      font-size: 0.86rem;
    }
  `,
})
export class NotificationsBell implements OnInit, OnDestroy {
  protected readonly service = inject(NotificationService);
  private readonly router = inject(Router);
  private readonly host = inject(ElementRef<HTMLElement>);
  private readonly destroyRef = inject(DestroyRef);

  readonly open = signal(false);

  ngOnInit(): void {
    this.service.refresh().subscribe({ error: () => {} });
    // Refresco periódico mientras el shell esté montado.
    interval(REFRESH_MS)
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(() => this.service.refresh().subscribe({ error: () => {} }));
    document.addEventListener('click', this.onDocClick, true);
  }

  ngOnDestroy(): void {
    document.removeEventListener('click', this.onDocClick, true);
  }

  toggle(): void {
    if (this.open()) {
      this.close();
    } else {
      this.open.set(true);
      this.service.refresh().subscribe({ error: () => {} });
    }
  }

  /** Al cerrar, descarta las alertas vistas (no reaparecen en la sesión). */
  close(): void {
    if (!this.open()) return;
    this.open.set(false);
    this.service.dismissAll();
  }

  go(alert: Alert): void {
    this.service.dismiss(alert.type);
    switch (alert.type) {
      case 'low_stock':
      case 'out_of_stock':
        this.router.navigate(['/products'], { queryParams: { low_stock: 1 } });
        break;
      case 'expiring':
        this.router.navigate(['/products'], { queryParams: { expiring_soon: 1 } });
        break;
      case 'expired':
        this.router.navigate(['/products'], { queryParams: { expired: 1 } });
        break;
      case 'cash_closed':
        this.router.navigate(['/cash-registers']);
        break;
    }
    this.close();
  }

  iconFor(type: Alert['type']): string {
    switch (type) {
      case 'low_stock':
      case 'out_of_stock':
        return 'inventory';
      case 'cash_closed':
        return 'cash';
      default:
        return 'alert';
    }
  }

  private readonly onDocClick = (event: MouseEvent): void => {
    if (this.open() && !this.host.nativeElement.contains(event.target as Node)) {
      this.close();
    }
  };
}
