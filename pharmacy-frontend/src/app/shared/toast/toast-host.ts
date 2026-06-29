import { ChangeDetectionStrategy, Component, inject } from '@angular/core';
import { ToastService } from './toast.service';

/** Contenedor que renderiza los toasts activos. Se monta una vez en el shell. */
@Component({
  selector: 'app-toast-host',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    <div class="toasts" role="status" aria-live="polite">
      @for (t of toast.toasts(); track t.id) {
        <div class="toast" [class]="'toast--' + t.variant" (click)="toast.dismiss(t.id)">
          {{ t.text }}
        </div>
      }
    </div>
  `,
  styles: `
    .toasts {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 100;
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 360px;
    }
    .toast {
      padding: 12px 16px;
      border-radius: 10px;
      background: var(--surface);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-lg);
      color: var(--ink);
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      border-left: 3px solid var(--muted);
      animation: slide-in 0.2s ease;
    }
    .toast--success { border-left-color: var(--success); }
    .toast--error { border-left-color: var(--danger); }
    .toast--info { border-left-color: var(--info); }
    @keyframes slide-in {
      from { opacity: 0; transform: translateX(12px); }
      to { opacity: 1; transform: translateX(0); }
    }
  `,
})
export class ToastHost {
  readonly toast = inject(ToastService);
}
