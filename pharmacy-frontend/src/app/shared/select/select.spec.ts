import { ComponentRef } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { SelectComponent, SelectOption } from './select';

describe('SelectComponent', () => {
  let fixture: ComponentFixture<SelectComponent>;
  let ref: ComponentRef<SelectComponent>;
  let cmp: SelectComponent;

  const NUM_OPTS: SelectOption[] = [
    { value: 1, label: 'Uno' },
    { value: 2, label: 'Dos' },
    { value: 3, label: 'Tres' },
  ];

  beforeEach(() => {
    TestBed.configureTestingModule({ imports: [SelectComponent] });
    fixture = TestBed.createComponent(SelectComponent);
    ref = fixture.componentRef;
    cmp = fixture.componentInstance;
  });

  function setOptions(opts: SelectOption[]): void {
    ref.setInput('options', opts);
    fixture.detectChanges();
  }

  it('writeValue sincroniza la etiqueta seleccionada (CVA read)', () => {
    setOptions(NUM_OPTS);
    cmp.writeValue(2);
    expect(cmp.selectedLabel()).toBe('Dos');
    expect(cmp.isSelected(2)).toBeTrue();
  });

  it('coerciona el valor entrante según valueType="number"', () => {
    ref.setInput('valueType', 'number');
    setOptions(NUM_OPTS);
    cmp.writeValue('2'); // llega como string desde fuera
    expect(cmp.selectedLabel()).toBe('Dos');
  });

  it('emite el valor con su tipo original al seleccionar (number, no string)', () => {
    ref.setInput('valueType', 'number');
    setOptions(NUM_OPTS);
    const onChange = jasmine.createSpy('onChange');
    const emitted: unknown[] = [];
    cmp.registerOnChange(onChange);
    cmp.valueChange.subscribe((v) => emitted.push(v));

    cmp.selectOption({ value: 3, label: 'Tres' });

    expect(onChange).toHaveBeenCalledWith(3);
    expect(emitted).toEqual([3]);
    expect(typeof emitted[0]).toBe('number');
  });

  it('no convierte cuando valueType="string"', () => {
    ref.setInput('valueType', 'string');
    setOptions([{ value: 'Administrador', label: 'Administrador' }]);
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    cmp.selectOption({ value: 'Administrador', label: 'Administrador' });
    expect(onChange).toHaveBeenCalledWith('Administrador');
  });

  it('activa la búsqueda automáticamente cuando supera el umbral', () => {
    const many = Array.from({ length: 10 }, (_, i) => ({ value: i, label: `Opción ${i}` }));
    setOptions(many);
    expect(cmp.effectiveSearchable()).toBeTrue();
  });

  it('respeta searchable=false aunque haya muchas opciones', () => {
    ref.setInput('searchable', false);
    const many = Array.from({ length: 20 }, (_, i) => ({ value: i, label: `Opción ${i}` }));
    setOptions(many);
    expect(cmp.effectiveSearchable()).toBeFalse();
  });

  it('filtra ignorando mayúsculas y acentos', () => {
    setOptions([
      { value: 1, label: 'Acetaminofén' },
      { value: 2, label: 'Ibuprofeno' },
    ]);
    cmp.query.set('acetaminofen');
    expect(cmp.filtered().length).toBe(1);
    expect(cmp.filtered()[0].label).toBe('Acetaminofén');
  });

  it('selecciona la opción activa con Enter', () => {
    setOptions(NUM_OPTS);
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    cmp.openPanel();
    cmp.activeIndex.set(1);
    cmp.onSearchKeydown(new KeyboardEvent('keydown', { key: 'Enter' }));
    expect(onChange).toHaveBeenCalledWith(2);
    expect(cmp.open()).toBeFalse();
  });

  it('limpia la selección emitiendo null', () => {
    setOptions(NUM_OPTS);
    cmp.writeValue(2);
    const onChange = jasmine.createSpy('onChange');
    cmp.registerOnChange(onChange);
    cmp.clear(new MouseEvent('click'));
    expect(onChange).toHaveBeenCalledWith(null);
    expect(cmp.selectedValue()).toBeNull();
  });

  it('refleja el estado inválido y aria-label en el trigger', () => {
    ref.setInput('invalid', true);
    ref.setInput('ariaLabel', 'Categoría');
    setOptions(NUM_OPTS);
    const trigger = fixture.nativeElement.querySelector('.select__trigger') as HTMLElement;
    expect(trigger.classList.contains('is-invalid')).toBeTrue();
    expect(trigger.getAttribute('aria-label')).toBe('Categoría');
  });

  it('setDisabledState bloquea la apertura del panel', () => {
    setOptions(NUM_OPTS);
    cmp.setDisabledState(true);
    cmp.openPanel();
    expect(cmp.open()).toBeFalse();
  });
});
