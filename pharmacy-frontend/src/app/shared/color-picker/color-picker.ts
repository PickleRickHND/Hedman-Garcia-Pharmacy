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

interface Rgb {
  r: number;
  g: number;
  b: number;
}

function clamp(n: number, min: number, max: number): number {
  return Math.min(max, Math.max(min, n));
}

function hexToRgb(hex: string): Rgb | null {
  const m = /^#?([0-9a-f]{6})$/i.exec(hex.trim());
  if (!m) return null;
  const n = parseInt(m[1], 16);
  return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
}

function rgbToHex(r: number, g: number, b: number): string {
  const h = (x: number) => clamp(Math.round(x), 0, 255).toString(16).padStart(2, '0');
  return `#${h(r)}${h(g)}${h(b)}`;
}

function rgbToHsv(r: number, g: number, b: number): { h: number; s: number; v: number } {
  r /= 255;
  g /= 255;
  b /= 255;
  const max = Math.max(r, g, b);
  const min = Math.min(r, g, b);
  const d = max - min;
  let h = 0;
  if (d !== 0) {
    if (max === r) h = ((g - b) / d) % 6;
    else if (max === g) h = (b - r) / d + 2;
    else h = (r - g) / d + 4;
    h *= 60;
    if (h < 0) h += 360;
  }
  return { h, s: max === 0 ? 0 : d / max, v: max };
}

function hsvToRgb(h: number, s: number, v: number): Rgb {
  const c = v * s;
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
  const m = v - c;
  let r = 0;
  let g = 0;
  let b = 0;
  if (h < 60) [r, g] = [c, x];
  else if (h < 120) [r, g] = [x, c];
  else if (h < 180) [g, b] = [c, x];
  else if (h < 240) [g, b] = [x, c];
  else if (h < 300) [r, b] = [x, c];
  else [r, b] = [c, x];
  return { r: (r + m) * 255, g: (g + m) * 255, b: (b + m) * 255 };
}

/**
 * Selector de color custom (reemplaza al <input type="color"> nativo). Modelo
 * hex `#rrggbb`, con área de saturación/brillo, slider de tono e inputs RGB.
 * Implementa ControlValueAccessor (formControlName / [formControl]).
 */
@Component({
  selector: 'app-color-picker',
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => ColorPickerComponent),
      multi: true,
    },
  ],
  template: `
    <div class="cp" [class.cp--open]="open()">
      <div class="cp__field input" [class.is-invalid]="invalid()" [class.cp__field--disabled]="isDisabled()">
        <button
          type="button"
          class="cp__swatch"
          [style.background]="hex()"
          [attr.aria-label]="ariaLabel() || 'Elegir color'"
          [attr.id]="inputId() || null"
          [attr.aria-expanded]="open()"
          [disabled]="isDisabled()"
          (click)="toggle()"
        ></button>
        <input
          type="text"
          class="cp__hex mono"
          autocomplete="off"
          spellcheck="false"
          aria-label="Código hexadecimal"
          [value]="hex()"
          [disabled]="isDisabled()"
          (input)="onHexInput($event)"
          (blur)="onHexBlur($event)"
        />
      </div>

      @if (open()) {
        <div class="cp__panel" role="dialog" aria-label="Selector de color">
          <div
            #sv
            class="cp__sv"
            [style.background]="svBackground()"
            (pointerdown)="startSv($event)"
          >
            <span class="cp__sv-thumb" [style.left.%]="sat() * 100" [style.top.%]="(1 - val()) * 100"></span>
          </div>

          <div class="cp__controls">
            <span class="cp__preview" [style.background]="hex()" aria-hidden="true"></span>
            <div
              #hue
              class="cp__hue"
              (pointerdown)="startHue($event)"
            >
              <span class="cp__hue-thumb" [style.left.%]="(hue_() / 360) * 100"></span>
            </div>
          </div>

          <div class="cp__rgb">
            <label class="cp__rgb-field">
              <input type="number" min="0" max="255" aria-label="Rojo" [value]="rgb().r" (input)="onRgbInput('r', $event)" />
              <span>R</span>
            </label>
            <label class="cp__rgb-field">
              <input type="number" min="0" max="255" aria-label="Verde" [value]="rgb().g" (input)="onRgbInput('g', $event)" />
              <span>G</span>
            </label>
            <label class="cp__rgb-field">
              <input type="number" min="0" max="255" aria-label="Azul" [value]="rgb().b" (input)="onRgbInput('b', $event)" />
              <span>B</span>
            </label>
          </div>
        </div>
      }
    </div>
  `,
  styles: `
    :host {
      display: block;
      position: relative;
    }
    .cp__field {
      display: flex;
      align-items: center;
      gap: 10px;
      padding-left: 6px;
    }
    .cp__field--disabled {
      opacity: 0.55;
    }
    .cp__swatch {
      width: 34px;
      height: 32px;
      border: 1px solid var(--border-strong);
      border-radius: 6px;
      cursor: pointer;
      flex-shrink: 0;
      padding: 0;
    }
    .cp__hex {
      flex: 1;
      min-width: 0;
      height: 100%;
      border: none;
      background: transparent;
      outline: none;
      font-size: 0.95rem;
      color: var(--ink);
      text-transform: lowercase;
    }
    .cp__panel {
      position: absolute;
      z-index: 70;
      top: calc(100% + 4px);
      left: 0;
      width: 240px;
      padding: 12px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow-lg);
    }
    .cp__sv {
      position: relative;
      width: 100%;
      height: 150px;
      border-radius: 6px;
      cursor: crosshair;
      touch-action: none;
      overflow: hidden;
    }
    .cp__sv-thumb,
    .cp__hue-thumb {
      position: absolute;
      width: 14px;
      height: 14px;
      border: 2px solid #fff;
      border-radius: 50%;
      box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.35);
      transform: translate(-50%, -50%);
      pointer-events: none;
    }
    .cp__controls {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 12px;
    }
    .cp__preview {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      border: 1px solid var(--border);
      flex-shrink: 0;
    }
    .cp__hue {
      position: relative;
      flex: 1;
      height: 12px;
      border-radius: 6px;
      cursor: pointer;
      touch-action: none;
      background: linear-gradient(
        to right,
        #f00 0%,
        #ff0 17%,
        #0f0 33%,
        #0ff 50%,
        #00f 67%,
        #f0f 83%,
        #f00 100%
      );
    }
    .cp__hue-thumb {
      top: 50%;
    }
    .cp__rgb {
      display: flex;
      gap: 8px;
      margin-top: 12px;
    }
    .cp__rgb-field {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2px;
      flex: 1;
    }
    .cp__rgb-field input {
      width: 100%;
      height: 34px;
      text-align: center;
      border: 1px solid var(--border-strong);
      border-radius: 6px;
      background: var(--surface);
      color: var(--ink);
      font-size: 0.85rem;
    }
    .cp__rgb-field span {
      font-size: 0.7rem;
      color: var(--muted);
      font-weight: 600;
    }
  `,
})
export class ColorPickerComponent implements ControlValueAccessor, OnInit, OnDestroy {
  private readonly host = inject(ElementRef<HTMLElement>);

  // --- API pública -------------------------------------------------------
  readonly disabled = input(false);
  readonly invalid = input(false);
  readonly ariaLabel = input('');
  readonly inputId = input('');
  readonly valueChange = output<string>();

  // --- Estado interno (HSV como fuente de verdad) ------------------------
  readonly hue_ = signal(174);
  readonly sat = signal(0.83);
  readonly val = signal(0.65);
  readonly open = signal(false);
  private readonly cvaDisabled = signal(false);

  private onChange: (v: string) => void = () => {};
  private onTouched: () => void = () => {};

  readonly isDisabled = computed(() => this.disabled() || this.cvaDisabled());
  readonly rgb = computed<Rgb>(() => {
    const { r, g, b } = hsvToRgb(this.hue_(), this.sat(), this.val());
    return { r: Math.round(r), g: Math.round(g), b: Math.round(b) };
  });
  readonly hex = computed(() => rgbToHex(this.rgb().r, this.rgb().g, this.rgb().b));
  /** Fondo del área SV: blanco→tono puro (horizontal) y transparente→negro (vertical). */
  readonly svBackground = computed(() => {
    const pure = hsvToRgb(this.hue_(), 1, 1);
    const c = rgbToHex(pure.r, pure.g, pure.b);
    return `linear-gradient(to bottom, transparent, #000), linear-gradient(to right, #fff, ${c})`;
  });

  // --- ControlValueAccessor ---------------------------------------------
  writeValue(value: string | null): void {
    const rgb = value ? hexToRgb(value) : null;
    if (rgb) {
      const { h, s, v } = rgbToHsv(rgb.r, rgb.g, rgb.b);
      // Conserva el tono actual cuando el color es acromático (s=0) para no saltar el slider.
      if (s > 0) this.hue_.set(h);
      this.sat.set(s);
      this.val.set(v);
    }
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
    this.open.update((o) => !o);
    if (!this.open()) this.onTouched();
  }

  closePanel(): void {
    if (!this.open()) return;
    this.open.set(false);
    this.onTouched();
  }

  onHexInput(event: Event): void {
    const raw = (event.target as HTMLInputElement).value;
    const rgb = hexToRgb(raw);
    if (rgb) {
      const { h, s, v } = rgbToHsv(rgb.r, rgb.g, rgb.b);
      if (s > 0) this.hue_.set(h);
      this.sat.set(s);
      this.val.set(v);
      this.commit();
    }
  }

  onHexBlur(event: Event): void {
    // Revierte el texto al hex válido actual si el usuario dejó algo inválido.
    const input = event.target as HTMLInputElement;
    if (!hexToRgb(input.value)) {
      input.value = this.hex();
    }
    this.onTouched();
  }

  onRgbInput(channel: 'r' | 'g' | 'b', event: Event): void {
    const value = clamp(Number((event.target as HTMLInputElement).value) || 0, 0, 255);
    const next = { ...this.rgb(), [channel]: value };
    const { h, s, v } = rgbToHsv(next.r, next.g, next.b);
    if (s > 0) this.hue_.set(h);
    this.sat.set(s);
    this.val.set(v);
    this.commit();
  }

  startSv(event: PointerEvent): void {
    if (this.isDisabled()) return;
    const el = event.currentTarget as HTMLElement;
    el.setPointerCapture(event.pointerId);
    this.updateSv(event, el);
    const move = (e: PointerEvent) => this.updateSv(e, el);
    const up = () => {
      el.removeEventListener('pointermove', move);
      el.removeEventListener('pointerup', up);
      this.onTouched();
    };
    el.addEventListener('pointermove', move);
    el.addEventListener('pointerup', up);
  }

  startHue(event: PointerEvent): void {
    if (this.isDisabled()) return;
    const el = event.currentTarget as HTMLElement;
    el.setPointerCapture(event.pointerId);
    this.updateHue(event, el);
    const move = (e: PointerEvent) => this.updateHue(e, el);
    const up = () => {
      el.removeEventListener('pointermove', move);
      el.removeEventListener('pointerup', up);
      this.onTouched();
    };
    el.addEventListener('pointermove', move);
    el.addEventListener('pointerup', up);
  }

  private updateSv(event: PointerEvent, el: HTMLElement): void {
    const rect = el.getBoundingClientRect();
    this.sat.set(clamp((event.clientX - rect.left) / rect.width, 0, 1));
    this.val.set(clamp(1 - (event.clientY - rect.top) / rect.height, 0, 1));
    this.commit();
  }

  private updateHue(event: PointerEvent, el: HTMLElement): void {
    const rect = el.getBoundingClientRect();
    this.hue_.set(clamp((event.clientX - rect.left) / rect.width, 0, 1) * 360);
    this.commit();
  }

  private commit(): void {
    const value = this.hex();
    this.onChange(value);
    this.valueChange.emit(value);
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
