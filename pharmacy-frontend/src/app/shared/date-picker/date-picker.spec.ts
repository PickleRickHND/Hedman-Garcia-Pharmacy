import { ComponentRef } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { DatePickerComponent } from './date-picker';

describe('DatePickerComponent', () => {
  let fixture: ComponentFixture<DatePickerComponent>;
  let ref: ComponentRef<DatePickerComponent>;
  let cmp: DatePickerComponent;

  beforeEach(() => {
    TestBed.configureTestingModule({ imports: [DatePickerComponent] });
    fixture = TestBed.createComponent(DatePickerComponent);
    ref = fixture.componentRef;
    cmp = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('writeValue convierte ISO a display dd/mm/yyyy', () => {
    cmp.writeValue('2026-06-29');
    expect(cmp.displayValue()).toBe('29/06/2026');
  });

  it('marca el día seleccionado en la grilla', () => {
    cmp.writeValue('2026-06-29');
    const selected = cmp.grid().filter((c) => c.selected);
    expect(selected.length).toBe(1);
    expect(selected[0].iso).toBe('2026-06-29');
  });

  it('emite ISO al elegir un día (CVA write)', () => {
    cmp.writeValue('2026-06-15');
    const onChange = jasmine.createSpy('onChange');
    const emitted: string[] = [];
    cmp.registerOnChange(onChange);
    cmp.valueChange.subscribe((v) => emitted.push(v));

    const cell = cmp.grid().find((c) => c.iso === '2026-06-20')!;
    cmp.pick(cell);

    expect(onChange).toHaveBeenCalledWith('2026-06-20');
    expect(emitted).toEqual(['2026-06-20']);
  });

  it('parsea texto dd/mm/yyyy a ISO', () => {
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    const input = document.createElement('input');
    input.value = '29/06/2026';
    cmp.onType({ target: input } as unknown as Event);
    expect(onChange).toHaveBeenCalledWith('2026-06-29');
  });

  it('no construye fechas con desfase UTC (día 1 permanece en el mes)', () => {
    cmp.writeValue('2026-06-15');
    const cell = cmp.grid().find((c) => c.iso === '2026-06-01')!;
    cmp.pick(cell);
    expect(cmp.displayValue()).toBe('01/06/2026');
  });

  it('deshabilita los días fuera del rango min/max', () => {
    ref.setInput('min', '2026-06-10');
    ref.setInput('max', '2026-06-20');
    cmp.writeValue('2026-06-15');
    fixture.detectChanges();
    const before = cmp.grid().find((c) => c.iso === '2026-06-05');
    const inside = cmp.grid().find((c) => c.iso === '2026-06-15');
    expect(before?.disabled).toBeTrue();
    expect(inside?.disabled).toBeFalse();
  });

  it('permite vaciar cuando nullable=true', () => {
    cmp.writeValue('2026-06-15');
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    cmp.clear();
    expect(onChange).toHaveBeenCalledWith('');
    expect(cmp.isoValue()).toBe('');
  });

  it('no vacía cuando nullable=false', () => {
    ref.setInput('nullable', false);
    cmp.writeValue('2026-06-15');
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    cmp.clear();
    expect(onChange).not.toHaveBeenCalled();
    expect(cmp.isoValue()).toBe('2026-06-15');
  });

  it('usa nombres de mes en español', () => {
    cmp.writeValue('2026-06-15');
    expect(cmp.monthName()).toBe('Junio');
  });

  it('navega entre meses con shiftMonth', () => {
    cmp.writeValue('2026-06-15');
    cmp.shiftMonth(1);
    expect(cmp.view()).toEqual({ y: 2026, m0: 6 }); // Julio
    cmp.shiftMonth(-2);
    expect(cmp.view()).toEqual({ y: 2026, m0: 4 }); // Mayo
  });
});
