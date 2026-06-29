import {
  ChangeDetectionStrategy,
  Component,
  ElementRef,
  OnDestroy,
  OnInit,
  computed,
  effect,
  forwardRef,
  inject,
  input,
  output,
  signal,
  viewChild,
} from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

export type SelectValue = string | number | null;

export interface SelectOption<T extends SelectValue = SelectValue> {
  value: T;
  label: string;
  disabled?: boolean;
}

/** Normaliza texto para búsquedas insensibles a mayúsculas y acentos. */
function fold(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, "");
}

/**
 * Dropdown custom (reemplaza al <select> nativo). Implementa ControlValueAccessor,
 * por lo que funciona con `formControlName`, `[formControl]` y también de forma
 * template-driven vía `[value]` + `(valueChange)`. Búsqueda automática en listas
 * largas, navegación por teclado y coerción de tipo (number/string).
 */
@Component({
  selector: 'app-select',
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => SelectComponent),
      multi: true,
    },
  ],
  template: `
    <div class="select" [class.select--open]="open()">
      <button
        #trigger
        type="button"
        class="input select__trigger"
        role="combobox"
        aria-haspopup="listbox"
        [attr.aria-expanded]="open()"
        [attr.aria-label]="ariaLabel() || null"
        [attr.id]="inputId() || null"
        [class.is-invalid]="invalid()"
        [class.select__trigger--placeholder]="!selectedLabel()"
        [disabled]="isDisabled()"
        (click)="toggle()"
        (keydown)="onTriggerKeydown($event)"
      >
        <span class="select__value">{{ selectedLabel() || placeholder() }}</span>
        @if (clearable() && selectedValue() !== null && !isDisabled()) {
          <span
            class="select__clear"
            role="button"
            aria-label="Limpiar selección"
            (click)="clear($event)"
            >&times;</span
          >
        }
        <span class="select__caret" aria-hidden="true">▾</span>
      </button>

      @if (open()) {
        <div class="select__panel" [class]="panelClass()">
          @if (effectiveSearchable()) {
            <input
              #searchBox
              type="text"
              class="select__search"
              [value]="query()"
              placeholder="Buscar…"
              aria-label="Buscar opción"
              (input)="onSearch($event)"
              (keydown)="onSearchKeydown($event)"
            />
          }
          <ul class="select__list" role="listbox" [attr.aria-label]="ariaLabel() || null">
            @for (opt of filtered(); track opt.value; let i = $index) {
              <li
                role="option"
                class="select__option"
                [class.select__option--active]="i === activeIndex()"
                [class.select__option--selected]="isSelected(opt.value)"
                [class.select__option--disabled]="opt.disabled"
                [attr.aria-selected]="isSelected(opt.value)"
                (mouseenter)="activeIndex.set(i)"
                (click)="selectOption(opt)"
              >
                {{ opt.label }}
              </li>
            } @empty {
              <li class="select__empty">Sin resultados</li>
            }
          </ul>
        </div>
      }
    </div>
  `,
  styles: `
    :host {
      display: block;
      position: relative;
    }
    .select__trigger {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      text-align: left;
      cursor: pointer;
    }
    .select__trigger:disabled {
      opacity: 0.55;
      cursor: not-allowed;
    }
    .select__value {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .select__trigger--placeholder .select__value {
      color: var(--muted);
    }
    .select__caret {
      color: var(--muted);
      font-size: 0.7rem;
      transition: transform 0.15s ease;
    }
    .select--open .select__caret {
      transform: rotate(180deg);
    }
    .select__clear {
      color: var(--muted);
      font-size: 1.1rem;
      line-height: 1;
      cursor: pointer;
      padding: 0 2px;
    }
    .select__clear:hover {
      color: var(--ink);
    }
    .select__panel {
      position: absolute;
      z-index: 70;
      top: calc(100% + 4px);
      left: 0;
      right: 0;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }
    .select__search {
      width: 100%;
      height: 38px;
      padding: 0 12px;
      border: none;
      border-bottom: 1px solid var(--border);
      font-size: 0.92rem;
      outline: none;
    }
    .select__list {
      list-style: none;
      margin: 0;
      padding: 4px;
      max-height: 240px;
      overflow-y: auto;
    }
    .select__option {
      padding: 9px 12px;
      border-radius: 6px;
      font-size: 0.92rem;
      cursor: pointer;
    }
    .select__option--active {
      background: var(--surface-2);
    }
    .select__option--selected {
      color: var(--accent);
      font-weight: 600;
    }
    .select__option--disabled {
      opacity: 0.45;
      pointer-events: none;
    }
    .select__empty {
      padding: 12px;
      color: var(--muted);
      font-size: 0.88rem;
      text-align: center;
    }
  `,
})
export class SelectComponent implements ControlValueAccessor, OnInit, OnDestroy {
  private readonly host = inject(ElementRef<HTMLElement>);

  // --- API pública -------------------------------------------------------
  readonly options = input<SelectOption[]>([]);
  readonly placeholder = input('Seleccione…');
  readonly disabled = input(false);
  readonly invalid = input(false);
  readonly searchable = input<boolean | undefined>(undefined);
  readonly searchThreshold = input(8);
  readonly valueType = input<'string' | 'number' | 'auto'>('auto');
  readonly ariaLabel = input('');
  readonly inputId = input('');
  readonly clearable = input(false);
  readonly panelClass = input('');
  /** Permite uso template-driven sin formControl: `[value]` + `(valueChange)`. */
  readonly value = input<SelectValue | undefined>(undefined);
  readonly valueChange = output<SelectValue>();

  // --- Estado interno ----------------------------------------------------
  readonly open = signal(false);
  readonly query = signal('');
  readonly activeIndex = signal(-1);
  readonly selectedValue = signal<SelectValue>(null);
  private readonly cvaDisabled = signal(false);
  private readonly searchBox = viewChild<ElementRef<HTMLInputElement>>('searchBox');
  private readonly triggerEl = viewChild<ElementRef<HTMLButtonElement>>('trigger');

  private onChange: (v: SelectValue) => void = () => {};
  private onTouched: () => void = () => {};
  /** True cuando el componente está enlazado a un FormControl (CVA es la fuente de verdad). */
  private isControlled = false;
  private typeAheadTimer: ReturnType<typeof setTimeout> | null = null;

  constructor() {
    // Soporte template-driven: cuando llega `[value]`, sincroniza el estado visual.
    // Si hay FormControl enlazado, el CVA (writeValue) manda y se ignora `[value]`.
    effect(() => {
      const v = this.value();
      if (v !== undefined && !this.isControlled) {
        this.selectedValue.set(this.coerce(v));
      }
    });
    // Enfoca el buscador al abrir el panel.
    effect(() => {
      if (this.open() && this.effectiveSearchable()) {
        queueMicrotask(() => this.searchBox()?.nativeElement.focus());
      }
    });
  }

  readonly isDisabled = computed(() => this.disabled() || this.cvaDisabled());

  private readonly numericMode = computed(() => {
    const t = this.valueType();
    if (t === 'number') return true;
    if (t === 'string') return false;
    const opts = this.options();
    return opts.length > 0 && opts.every((o) => typeof o.value === 'number');
  });

  readonly effectiveSearchable = computed(
    () => this.searchable() ?? this.options().length > this.searchThreshold(),
  );

  readonly filtered = computed<SelectOption[]>(() => {
    const q = fold(this.query().trim());
    const opts = this.options();
    if (!q) return opts;
    return opts.filter((o) => fold(o.label).includes(q));
  });

  readonly selectedLabel = computed(() => {
    const v = this.selectedValue();
    return this.options().find((o) => this.same(o.value, v))?.label ?? '';
  });

  isSelected(v: SelectValue): boolean {
    return this.same(v, this.selectedValue());
  }

  // --- ControlValueAccessor ---------------------------------------------
  writeValue(value: SelectValue): void {
    this.selectedValue.set(this.coerce(value));
  }
  registerOnChange(fn: (v: SelectValue) => void): void {
    this.onChange = fn;
    this.isControlled = true;
  }
  registerOnTouched(fn: () => void): void {
    this.onTouched = fn;
  }
  setDisabledState(isDisabled: boolean): void {
    this.cvaDisabled.set(isDisabled);
  }

  // --- Interacción -------------------------------------------------------
  toggle(): void {
    this.open() ? this.closePanel() : this.openPanel();
  }

  openPanel(): void {
    if (this.isDisabled()) return;
    this.query.set('');
    this.open.set(true);
    // Posiciona el item activo en el seleccionado (o el primero).
    const list = this.filtered();
    const idx = list.findIndex((o) => this.same(o.value, this.selectedValue()));
    this.activeIndex.set(idx >= 0 ? idx : 0);
  }

  closePanel(): void {
    if (!this.open()) return;
    this.open.set(false);
    this.onTouched();
    queueMicrotask(() => this.triggerEl()?.nativeElement.focus());
  }

  selectOption(opt: SelectOption): void {
    if (opt.disabled) return;
    const v = this.coerce(opt.value);
    this.selectedValue.set(v);
    this.onChange(v);
    this.valueChange.emit(v);
    this.closePanel();
  }

  clear(event: Event): void {
    event.stopPropagation();
    this.selectedValue.set(null);
    this.onChange(null);
    this.valueChange.emit(null);
    this.onTouched();
  }

  onSearch(event: Event): void {
    this.query.set((event.target as HTMLInputElement).value);
    this.activeIndex.set(0);
  }

  onTriggerKeydown(event: KeyboardEvent): void {
    if (!this.open()) {
      if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
        event.preventDefault();
        this.openPanel();
      }
      return;
    }
    // Panel abierto sin buscador: navegación + type-ahead desde el trigger.
    if (this.handleListKeys(event)) return;
    if (!this.effectiveSearchable() && event.key.length === 1 && /\S/.test(event.key)) {
      this.typeAhead(event.key);
    }
  }

  onSearchKeydown(event: KeyboardEvent): void {
    this.handleListKeys(event);
  }

  private handleListKeys(event: KeyboardEvent): boolean {
    const list = this.filtered();
    switch (event.key) {
      case 'ArrowDown':
        event.preventDefault();
        this.move(1);
        return true;
      case 'ArrowUp':
        event.preventDefault();
        this.move(-1);
        return true;
      case 'Home':
        event.preventDefault();
        this.activeIndex.set(0);
        return true;
      case 'End':
        event.preventDefault();
        this.activeIndex.set(list.length - 1);
        return true;
      case 'Enter':
        event.preventDefault();
        if (list[this.activeIndex()]) this.selectOption(list[this.activeIndex()]);
        return true;
      case 'Escape':
        event.preventDefault();
        this.closePanel();
        return true;
      case 'Tab':
        this.closePanel();
        return false;
      default:
        return false;
    }
  }

  private move(delta: number): void {
    const len = this.filtered().length;
    if (len === 0) return;
    const next = (this.activeIndex() + delta + len) % len;
    this.activeIndex.set(next);
  }

  private typeBuffer = '';
  private typeAhead(key: string): void {
    this.typeBuffer += key.toLowerCase();
    const list = this.filtered();
    const idx = list.findIndex((o) => fold(o.label).startsWith(this.typeBuffer));
    if (idx >= 0) this.activeIndex.set(idx);
    // Reinicia el buffer tras una pausa (debounce: cancela el timer previo).
    if (this.typeAheadTimer !== null) clearTimeout(this.typeAheadTimer);
    this.typeAheadTimer = setTimeout(() => (this.typeBuffer = ''), 600);
  }

  /** Cierra el panel al hacer click fuera del componente. */
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
    if (this.typeAheadTimer !== null) clearTimeout(this.typeAheadTimer);
  }

  // --- Coerción / comparación de valores --------------------------------
  /** Trata null/undefined/'' como "vacío": null en modo numérico, '' en modo string. */
  private isEmpty(v: SelectValue | undefined): boolean {
    return v === null || v === undefined || v === '';
  }

  private coerce(v: SelectValue | undefined): SelectValue {
    if (this.isEmpty(v)) return this.numericMode() ? null : '';
    return this.numericMode() ? Number(v) : (v as SelectValue);
  }

  private same(a: SelectValue, b: SelectValue): boolean {
    // Vacío matchea vacío: null y '' representan la misma opción "sin valor".
    if (this.isEmpty(a) || this.isEmpty(b)) return this.isEmpty(a) === this.isEmpty(b);
    if (this.numericMode()) return Number(a) === Number(b);
    return String(a) === String(b);
  }
}
