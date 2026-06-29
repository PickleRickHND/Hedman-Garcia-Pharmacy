import { ChangeDetectionStrategy, Component, computed, input } from '@angular/core';

export interface BarDatum {
  label: string;
  value: number;
  /** Texto a mostrar a la derecha (ej. moneda formateada). Default: el valor. */
  display?: string;
}

/**
 * Gráfico de barras horizontal, liviano (CSS puro, sin dependencias).
 * El ancho de cada barra es proporcional al máximo del conjunto.
 */
@Component({
  selector: 'app-bar-chart',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (data().length === 0) {
      <p class="bar-chart__empty">Sin datos para graficar.</p>
    } @else {
      <div class="bar-chart">
        @for (d of data(); track d.label) {
          <div class="bar-chart__row">
            <span class="bar-chart__label" [title]="d.label">{{ d.label }}</span>
            <span class="bar-chart__track">
              <span class="bar-chart__fill" [style.width.%]="pct(d.value)"></span>
            </span>
            <span class="bar-chart__value">{{ d.display ?? d.value }}</span>
          </div>
        }
      </div>
    }
  `,
  styles: `
    .bar-chart { display: flex; flex-direction: column; gap: 12px; }
    .bar-chart__empty { color: var(--muted); font-size: 0.88rem; padding: 12px 0; }
    .bar-chart__row {
      display: grid;
      grid-template-columns: minmax(90px, 160px) 1fr auto;
      align-items: center;
      gap: 12px;
    }
    .bar-chart__label {
      font-size: 0.85rem;
      color: var(--ink-soft);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .bar-chart__track {
      height: 12px;
      border-radius: 999px;
      background: var(--surface-2);
      overflow: hidden;
    }
    .bar-chart__fill {
      display: block;
      height: 100%;
      border-radius: 999px;
      background: var(--accent);
      min-width: 2px;
      transition: width 0.3s ease;
    }
    .bar-chart__value { font-size: 0.85rem; font-weight: 600; color: var(--ink); white-space: nowrap; }
  `,
})
export class BarChart {
  readonly data = input.required<BarDatum[]>();

  private readonly max = computed(() => Math.max(1, ...this.data().map((d) => d.value)));

  pct(value: number): number {
    return Math.round((value / this.max()) * 100);
  }
}
