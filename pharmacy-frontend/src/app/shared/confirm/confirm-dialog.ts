import { ChangeDetectionStrategy, Component, input, output } from '@angular/core';

/** Diálogo de confirmación reutilizable. El padre controla `open`. */
@Component({
  selector: 'app-confirm-dialog',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (open()) {
      <div class="overlay" (click)="cancelled.emit()">
        <div class="dialog" (click)="$event.stopPropagation()" role="dialog" aria-modal="true">
          <h3 class="dialog__title">{{ title() }}</h3>
          <p class="dialog__msg">{{ message() }}</p>
          <div class="dialog__actions">
            <button type="button" class="btn btn-ghost" (click)="cancelled.emit()">Cancelar</button>
            <button type="button" class="btn btn-danger" (click)="confirmed.emit()">
              {{ confirmLabel() }}
            </button>
          </div>
        </div>
      </div>
    }
  `,
  styles: `
    .overlay {
      position: fixed;
      inset: 0;
      z-index: 80;
      display: grid;
      place-items: center;
      padding: 20px;
      background: rgba(8, 12, 20, 0.5);
      backdrop-filter: blur(2px);
      animation: overlay-in 0.2s ease both;
    }
    .dialog {
      width: 100%;
      max-width: 400px;
      padding: 24px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      animation: dialog-in 0.24s var(--ease-spring, cubic-bezier(0.34, 1.4, 0.64, 1)) both;
    }
    @keyframes overlay-in { from { opacity: 0; } to { opacity: 1; } }
    @keyframes dialog-in {
      from { opacity: 0; transform: translateY(12px) scale(0.97); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @media (prefers-reduced-motion: reduce) {
      .overlay, .dialog { animation-duration: 0.001ms; }
    }
    .dialog__title { font-size: 1.15rem; margin-bottom: 8px; }
    .dialog__msg { color: var(--muted); font-size: 0.92rem; margin-bottom: 20px; }
    .dialog__actions { display: flex; justify-content: flex-end; gap: 10px; }
    .btn-danger { background: var(--danger); border-color: var(--danger); color: #fff; }
    .btn-danger:hover { filter: brightness(0.94); }
  `,
})
export class ConfirmDialog {
  readonly open = input(false);
  readonly title = input('¿Confirmar?');
  readonly message = input('Esta acción no se puede deshacer.');
  readonly confirmLabel = input('Eliminar');
  readonly confirmed = output<void>();
  readonly cancelled = output<void>();
}
