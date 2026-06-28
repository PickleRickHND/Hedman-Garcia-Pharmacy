import { ChangeDetectionStrategy, Component, input, output } from '@angular/core';

/** Controles de paginación a partir del `meta` de una respuesta paginada de Laravel. */
@Component({
  selector: 'app-pagination',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (lastPage() > 1) {
      <nav class="pager" aria-label="Paginación">
        <button
          type="button"
          class="pager__btn"
          [disabled]="currentPage() <= 1"
          (click)="pageChange.emit(currentPage() - 1)"
        >
          Anterior
        </button>
        <span class="pager__info num">{{ currentPage() }} / {{ lastPage() }}</span>
        <button
          type="button"
          class="pager__btn"
          [disabled]="currentPage() >= lastPage()"
          (click)="pageChange.emit(currentPage() + 1)"
        >
          Siguiente
        </button>
      </nav>
    }
  `,
  styles: `
    .pager {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 12px;
      padding: 14px 4px 2px;
    }
    .pager__info { color: var(--muted); font-size: 0.85rem; }
    .pager__btn {
      height: 36px;
      padding: 0 14px;
      border: 1px solid var(--border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface);
      color: var(--ink);
      font-size: 0.85rem;
      font-weight: 600;
    }
    .pager__btn:hover:not(:disabled) { background: var(--surface-2); }
    .pager__btn:disabled { opacity: 0.5; cursor: not-allowed; }
  `,
})
export class Pagination {
  readonly currentPage = input.required<number>();
  readonly lastPage = input.required<number>();
  readonly pageChange = output<number>();
}
