import {
  ChangeDetectionStrategy,
  Component,
  ElementRef,
  OnDestroy,
  OnInit,
  computed,
  forwardRef,
  inject,
  input,
  output,
  signal,
} from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

const MONTHS = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];
const WEEKDAYS = ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'];

interface DayCell {
  day: number;
  iso: string;
  inMonth: boolean;
  today: boolean;
  selected: boolean;
  disabled: boolean;
}

function pad(n: number): string {
  return String(n).padStart(2, '0');
}
/** Construye ISO YYYY-MM-DD en horario local (evita el desfase UTC de new Date(iso)). */
function toIso(y: number, m0: number, d: number): string {
  return `${y}-${pad(m0 + 1)}-${pad(d)}`;
}
function parseIso(iso: string | null): { y: number; m0: number; d: number } | null {
  if (!iso) return null;
  const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso);
  if (!match) return null;
  return { y: +match[1], m0: +match[2] - 1, d: +match[3] };
}
function isoToDisplay(iso: string): string {
  const p = parseIso(iso);
  return p ? `${pad(p.d)}/${pad(p.m0 + 1)}/${p.y}` : '';
}
/** Parsea dd/mm/yyyy (tolerante con separadores) a ISO; null si es inválida. */
function displayToIso(text: string): string | null {
  const match = /^(\d{1,2})[/.-](\d{1,2})[/.-](\d{2,4})$/.exec(text.trim());
  if (!match) return null;
  let [, d, m, y] = match;
  const yy = y.length === 2 ? `20${y}` : y;
  const day = +d;
  const mon = +m;
  if (mon < 1 || mon > 12 || day < 1 || day > 31) return null;
  const date = new Date(+yy, mon - 1, day);
  // Rechaza fechas imposibles (ej. 31/02) que JS normaliza.
  if (date.getMonth() !== mon - 1 || date.getDate() !== day) return null;
  return toIso(+yy, mon - 1, day);
}

/**
 * Date picker custom (reemplaza al <input type="date">). Modelo ISO YYYY-MM-DD,
 * display dd/mm/yyyy, calendario propio en español. Implementa ControlValueAccessor.
 */
@Component({
  selector: 'app-date-picker',
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => DatePickerComponent),
      multi: true,
    },
  ],
  template: `
    @if (label()) {
      <span class="date-field__label">{{ label() }}</span>
    }
    <div class="dp" [class.dp--open]="open()">
      <div class="dp__control input" [class.is-invalid]="invalid()" [class.dp__control--disabled]="isDisabled()">
        <input
          #textInput
          type="text"
          class="dp__input"
          inputmode="numeric"
          autocomplete="off"
          [attr.id]="inputId() || null"
          [attr.aria-label]="ariaLabel() || label() || 'Fecha'"
          [placeholder]="placeholder()"
          [value]="displayValue()"
          [disabled]="isDisabled()"
          (input)="onType($event)"
          (blur)="onBlur($event)"
          (keydown)="onInputKeydown($event)"
        />
        @if (nullable() && isoValue() && !isDisabled()) {
          <button type="button" class="dp__clear" aria-label="Limpiar fecha" (click)="clear()">&times;</button>
        }
        <button
          type="button"
          class="dp__toggle"
          aria-label="Abrir calendario"
          [disabled]="isDisabled()"
          (click)="toggle()"
        >
          ▾
        </button>
      </div>

      @if (open()) {
        <div class="dp__panel" role="dialog" aria-label="Calendario">
          <div class="dp__nav">
            <button type="button" class="dp__navbtn" aria-label="Mes anterior" (click)="shiftMonth(-1)">
              &lsaquo;
            </button>
            <span class="dp__title">{{ monthName() }} {{ view().y }}</span>
            <button type="button" class="dp__navbtn" aria-label="Mes siguiente" (click)="shiftMonth(1)">
              &rsaquo;
            </button>
          </div>
          <div class="dp__grid dp__grid--head">
            @for (w of weekdays; track w) {
              <span class="dp__dow">{{ w }}</span>
            }
          </div>
          <div class="dp__grid">
            @for (cell of grid(); track cell.iso) {
              <button
                type="button"
                class="dp__day"
                [class.dp__day--out]="!cell.inMonth"
                [class.dp__day--today]="cell.today"
                [class.dp__day--selected]="cell.selected"
                [disabled]="cell.disabled"
                (click)="pick(cell)"
              >
                {{ cell.day }}
              </button>
            }
          </div>
        </div>
      }
    </div>
  `,
  styles: `
    :host {
      display: inline-flex;
      flex-direction: column;
      gap: 3px;
    }
    .date-field__label {
      font-size: 0.72rem;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .dp {
      position: relative;
    }
    .dp__control {
      display: flex;
      align-items: center;
      gap: 2px;
      padding-right: 6px;
    }
    .dp__control--disabled {
      opacity: 0.55;
    }
    .dp__input {
      flex: 1;
      min-width: 0;
      height: 100%;
      border: none;
      background: transparent;
      outline: none;
      font-size: 0.95rem;
      color: var(--ink);
    }
    .dp__clear,
    .dp__toggle {
      border: none;
      background: transparent;
      color: var(--muted);
      cursor: pointer;
      padding: 0 4px;
      font-size: 0.9rem;
      line-height: 1;
    }
    .dp__clear {
      font-size: 1.1rem;
    }
    .dp__toggle:hover,
    .dp__clear:hover {
      color: var(--ink);
    }
    .dp__panel {
      position: absolute;
      z-index: 70;
      top: calc(100% + 4px);
      left: 0;
      width: 252px;
      padding: 10px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow-lg);
    }
    .dp__nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 8px;
    }
    .dp__title {
      font-size: 0.9rem;
      font-weight: 600;
    }
    .dp__navbtn {
      width: 28px;
      height: 28px;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: var(--surface);
      cursor: pointer;
      font-size: 1rem;
      line-height: 1;
    }
    .dp__navbtn:hover {
      background: var(--surface-2);
    }
    .dp__grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 2px;
    }
    .dp__grid--head {
      margin-bottom: 4px;
    }
    .dp__dow {
      text-align: center;
      font-size: 0.68rem;
      font-weight: 600;
      color: var(--muted);
      padding: 2px 0;
    }
    .dp__day {
      aspect-ratio: 1;
      border: none;
      border-radius: 6px;
      background: transparent;
      cursor: pointer;
      font-size: 0.82rem;
      color: var(--ink);
    }
    .dp__day:hover:not(:disabled) {
      background: var(--surface-2);
    }
    .dp__day--out {
      color: var(--muted);
      opacity: 0.5;
    }
    .dp__day--today {
      outline: 1px solid var(--accent);
    }
    .dp__day--selected {
      background: var(--accent);
      color: #fff;
      font-weight: 600;
    }
    .dp__day:disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }
  `,
})
export class DatePickerComponent implements ControlValueAccessor, OnInit, OnDestroy {
  private readonly host = inject(ElementRef<HTMLElement>);
  readonly weekdays = WEEKDAYS;

  // --- API pública -------------------------------------------------------
  readonly placeholder = input('dd/mm/aaaa');
  readonly disabled = input(false);
  readonly invalid = input(false);
  readonly min = input<string | null>(null);
  readonly max = input<string | null>(null);
  readonly nullable = input(true);
  readonly label = input('');
  readonly ariaLabel = input('');
  readonly inputId = input('');
  readonly valueChange = output<string>();

  // --- Estado interno ----------------------------------------------------
  readonly isoValue = signal<string>('');
  readonly open = signal(false);
  readonly view = signal<{ y: number; m0: number }>(this.initialView());
  private readonly cvaDisabled = signal(false);

  private onChange: (v: string) => void = () => {};
  private onTouched: () => void = () => {};

  readonly isDisabled = computed(() => this.disabled() || this.cvaDisabled());
  readonly displayValue = computed(() => isoToDisplay(this.isoValue()));
  readonly monthName = computed(() => MONTHS[this.view().m0]);

  readonly grid = computed<DayCell[]>(() => {
    const { y, m0 } = this.view();
    const todayIso = this.todayIso();
    const selected = this.isoValue();
    const first = new Date(y, m0, 1);
    // Offset para que la semana empiece en lunes (getDay: 0=Dom).
    const offset = (first.getDay() + 6) % 7;
    const start = new Date(y, m0, 1 - offset);
    const cells: DayCell[] = [];
    for (let i = 0; i < 42; i++) {
      const date = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
      const iso = toIso(date.getFullYear(), date.getMonth(), date.getDate());
      cells.push({
        day: date.getDate(),
        iso,
        inMonth: date.getMonth() === m0,
        today: iso === todayIso,
        selected: iso === selected,
        disabled: this.outOfRange(iso),
      });
    }
    return cells;
  });

  // --- ControlValueAccessor ---------------------------------------------
  writeValue(value: string | null): void {
    const iso = value ?? '';
    this.isoValue.set(iso);
    const p = parseIso(iso);
    if (p) this.view.set({ y: p.y, m0: p.m0 });
  }
  registerOnChange(fn: (v: string) => void): void {
    this.onChange = fn;
  }
  registerOnTouched(fn: () => void): void {
    this.onTouched = fn;
  }
  setDisabledState(isDisabled: boolean): void {
    this.cvaDisabled.set(isDisabled);
  }

  // --- Interacción -------------------------------------------------------
  toggle(): void {
    if (this.isDisabled()) return;
    if (this.open()) {
      this.closePanel();
    } else {
      const p = parseIso(this.isoValue());
      if (p) this.view.set({ y: p.y, m0: p.m0 });
      this.open.set(true);
    }
  }

  closePanel(): void {
    if (!this.open()) return;
    this.open.set(false);
    this.onTouched();
  }

  shiftMonth(delta: number): void {
    const { y, m0 } = this.view();
    const date = new Date(y, m0 + delta, 1);
    this.view.set({ y: date.getFullYear(), m0: date.getMonth() });
  }

  pick(cell: DayCell): void {
    if (cell.disabled) return;
    this.commit(cell.iso);
    this.closePanel();
  }

  clear(): void {
    if (!this.nullable()) return;
    this.commit('');
  }

  onType(event: Event): void {
    const raw = (event.target as HTMLInputElement).value;
    const iso = displayToIso(raw);
    // Solo commitea cuando el texto forma una fecha válida y en rango.
    if (iso && !this.outOfRange(iso)) {
      this.commit(iso, false);
    }
  }

  onBlur(event: Event): void {
    const raw = (event.target as HTMLInputElement).value.trim();
    if (raw === '') {
      if (this.nullable()) this.commit('', false);
    } else {
      const iso = displayToIso(raw);
      if (!iso || this.outOfRange(iso)) {
        // Revierte al último valor válido si el texto es inválido.
        (event.target as HTMLInputElement).value = this.displayValue();
      }
    }
    this.onTouched();
  }

  onInputKeydown(event: KeyboardEvent): void {
    if (event.key === 'ArrowDown' && !this.open()) {
      event.preventDefault();
      this.toggle();
    } else if (event.key === 'Escape' && this.open()) {
      event.preventDefault();
      this.closePanel();
    } else if (event.key === 'Enter') {
      event.preventDefault();
      this.closePanel();
    }
  }

  private commit(iso: string, close = true): void {
    this.isoValue.set(iso);
    this.onChange(iso);
    this.valueChange.emit(iso);
    const p = parseIso(iso);
    if (p) this.view.set({ y: p.y, m0: p.m0 });
    if (close) this.onTouched();
  }

  private outOfRange(iso: string): boolean {
    const min = this.min();
    const max = this.max();
    if (min && iso < min) return true;
    if (max && iso > max) return true;
    return false;
  }

  private todayIso(): string {
    const now = new Date();
    return toIso(now.getFullYear(), now.getMonth(), now.getDate());
  }

  private initialView(): { y: number; m0: number } {
    const now = new Date();
    return { y: now.getFullYear(), m0: now.getMonth() };
  }

  // --- Cierre al click fuera --------------------------------------------
  private readonly onDocClick = (event: MouseEvent): void => {
    if (this.open() && !this.host.nativeElement.contains(event.target as Node)) {
      this.closePanel();
    }
  };

  ngOnInit(): void {
    document.addEventListener('click', this.onDocClick, true);
  }
  ngOnDestroy(): void {
    document.removeEventListener('click', this.onDocClick, true);
  }
}
