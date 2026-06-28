import { Component, inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Icon } from '../icon/icon';

/** Placeholder para módulos cuyo frontend aún no se ha construido. */
@Component({
  selector: 'app-coming-soon',
  imports: [Icon],
  template: `
    <div class="cs">
      <span class="cs__icon"><app-icon [name]="icon" /></span>
      <h1>{{ title }}</h1>
      <p>Este módulo se conectará a la API en una próxima iteración.</p>
    </div>
  `,
  styles: `
    .cs {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      text-align: center;
      padding: 80px 24px;
      color: var(--muted);
    }
    .cs__icon {
      display: grid;
      place-items: center;
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: var(--surface-2);
      color: var(--accent);
      --icon-size: 26px;
      margin-bottom: 6px;
    }
    .cs h1 { font-size: 1.4rem; color: var(--ink); }
  `,
})
export class ComingSoon {
  private readonly route = inject(ActivatedRoute);
  readonly title = (this.route.snapshot.data['title'] as string) ?? 'Módulo';
  readonly icon = (this.route.snapshot.data['icon'] as string) ?? 'dashboard';
}
