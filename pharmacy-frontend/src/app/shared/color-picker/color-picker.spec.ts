import { ComponentRef } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ColorPickerComponent } from './color-picker';

describe('ColorPickerComponent', () => {
  let fixture: ComponentFixture<ColorPickerComponent>;
  let ref: ComponentRef<ColorPickerComponent>;
  let cmp: ColorPickerComponent;

  beforeEach(() => {
    TestBed.configureTestingModule({ imports: [ColorPickerComponent] });
    fixture = TestBed.createComponent(ColorPickerComponent);
    ref = fixture.componentRef;
    cmp = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('writeValue parsea el hex y reproduce el mismo color (CVA read)', () => {
    cmp.writeValue('#12a594');
    expect(cmp.hex()).toBe('#12a594');
    expect(cmp.rgb()).toEqual({ r: 18, g: 165, b: 148 });
  });

  it('round-trip de varios colores hex', () => {
    for (const c of ['#000000', '#ffffff', '#ff0000', '#00ff00', '#0000ff', '#abcdef']) {
      cmp.writeValue(c);
      expect(cmp.hex()).toBe(c);
    }
  });

  it('emite el hex al editar un canal RGB (CVA write)', () => {
    cmp.writeValue('#000000');
    const onChange = jasmine.createSpy('onChange');
    const emitted: string[] = [];
    cmp.registerOnChange(onChange);
    cmp.valueChange.subscribe((v) => emitted.push(v));

    const input = document.createElement('input');
    input.value = '255';
    cmp.onRgbInput('r', { target: input } as unknown as Event);

    expect(cmp.rgb().r).toBe(255);
    expect(onChange).toHaveBeenCalledWith('#ff0000');
    expect(emitted).toEqual(['#ff0000']);
  });

  it('actualiza desde el input hex cuando es válido', () => {
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    const input = document.createElement('input');
    input.value = '#abcdef';
    cmp.onHexInput({ target: input } as unknown as Event);
    expect(cmp.hex()).toBe('#abcdef');
    expect(onChange).toHaveBeenCalledWith('#abcdef');
  });

  it('ignora un hex inválido sin emitir', () => {
    cmp.writeValue('#12a594');
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    const input = document.createElement('input');
    input.value = 'noesuncolor';
    cmp.onHexInput({ target: input } as unknown as Event);
    expect(onChange).not.toHaveBeenCalled();
    expect(cmp.hex()).toBe('#12a594');
  });

  it('clampa valores RGB fuera de rango', () => {
    cmp.writeValue('#000000');
    const input = document.createElement('input');
    input.value = '999';
    cmp.onRgbInput('g', { target: input } as unknown as Event);
    expect(cmp.rgb().g).toBe(255);
  });

  it('conserva el tono en colores acromáticos (no salta el slider)', () => {
    cmp.writeValue('#ff0000'); // hue 0
    cmp.hue_.set(200);
    cmp.writeValue('#808080'); // gris: s=0, debe conservar hue 200
    expect(cmp.hue_()).toBe(200);
  });

  it('abre y cierra el panel', () => {
    expect(cmp.open()).toBeFalse();
    cmp.toggle();
    expect(cmp.open()).toBeTrue();
    cmp.closePanel();
    expect(cmp.open()).toBeFalse();
  });

  it('setDisabledState bloquea la apertura', () => {
    cmp.setDisabledState(true);
    cmp.toggle();
    expect(cmp.open()).toBeFalse();
  });
});
