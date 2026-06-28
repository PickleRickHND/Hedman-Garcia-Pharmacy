import { Injectable, signal } from '@angular/core';

export interface Toast {
  id: number;
  text: string;
  variant: 'success' | 'error' | 'info';
}

/** Notificaciones efímeras (toasts), consumidas por el ToastHost en el shell. */
@Injectable({ providedIn: 'root' })
export class ToastService {
  readonly toasts = signal<Toast[]>([]);
  private seq = 0;

  success(text: string): void {
    this.show(text, 'success');
  }

  error(text: string): void {
    this.show(text, 'error');
  }

  info(text: string): void {
    this.show(text, 'info');
  }

  dismiss(id: number): void {
    this.toasts.update((list) => list.filter((t) => t.id !== id));
  }

  private show(text: string, variant: Toast['variant']): void {
    const id = ++this.seq;
    this.toasts.update((list) => [...list, { id, text, variant }]);
    setTimeout(() => this.dismiss(id), 4000);
  }
}
