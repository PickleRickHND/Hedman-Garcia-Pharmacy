import { ComponentRef } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { BarChart, BarDatum } from './bar-chart';

describe('BarChart', () => {
  let fixture: ComponentFixture<BarChart>;
  let ref: ComponentRef<BarChart>;

  beforeEach(() => {
    TestBed.configureTestingModule({ imports: [BarChart] });
    fixture = TestBed.createComponent(BarChart);
    ref = fixture.componentRef;
  });

  function setData(data: BarDatum[]): void {
    ref.setInput('data', data);
    fixture.detectChanges();
  }

  it('calcula el porcentaje relativo al máximo', () => {
    setData([
      { label: 'A', value: 50 },
      { label: 'B', value: 100 },
    ]);
    const cmp = fixture.componentInstance;
    expect(cmp.pct(100)).toBe(100);
    expect(cmp.pct(50)).toBe(50);
    expect(cmp.pct(0)).toBe(0);
  });

  it('evita división por cero cuando todos los valores son 0', () => {
    setData([
      { label: 'A', value: 0 },
      { label: 'B', value: 0 },
    ]);
    expect(fixture.componentInstance.pct(0)).toBe(0);
  });

  it('muestra el estado vacío sin datos', () => {
    setData([]);
    const el = fixture.nativeElement as HTMLElement;
    expect(el.querySelector('.bar-chart__empty')).not.toBeNull();
    expect(el.querySelector('.bar-chart')).toBeNull();
  });

  it('renderiza una fila por dato y usa display si está presente', () => {
    setData([
      { label: 'Efectivo', value: 270, display: 'L 270.00' },
      { label: 'Tarjeta', value: 100 },
    ]);
    const el = fixture.nativeElement as HTMLElement;
    expect(el.querySelectorAll('.bar-chart__row').length).toBe(2);
    expect(el.textContent).toContain('L 270.00');
  });
});
