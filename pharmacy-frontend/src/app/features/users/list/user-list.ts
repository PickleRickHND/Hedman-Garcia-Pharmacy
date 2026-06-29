import { Component, DestroyRef, inject, OnInit, signal } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { AuthService } from '../../../core/auth/auth.service';
import { User } from '../../../core/models/user.model';
import { ConfirmDialog } from '../../../shared/confirm/confirm-dialog';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { ToastService } from '../../../shared/toast/toast.service';
import { UserAdminService } from '../user-admin.service';

@Component({
  selector: 'app-user-list',
  imports: [ReactiveFormsModule, RouterLink, Icon, Pagination, ConfirmDialog],
  templateUrl: './user-list.html',
})
export class UserList implements OnInit {
  private readonly service = inject(UserAdminService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);
  private readonly destroyRef = inject(DestroyRef);

  /** Id del usuario autenticado: no puede eliminarse a sí mismo. */
  readonly currentUserId = this.auth.user()?.id ?? null;

  readonly search = new FormControl('', { nonNullable: true });
  readonly roleFilter = new FormControl('', { nonNullable: true });
  readonly page = signal(1);
  readonly users = signal<User[]>([]);
  readonly roles = signal<string[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly loading = signal(true);
  readonly toDelete = signal<User | null>(null);

  ngOnInit(): void {
    this.service.roles().subscribe({
      next: (res) => this.roles.set(res.data),
      error: () => this.toast.error('No pudimos cargar los roles.'),
    });

    this.search.valueChanges
      .pipe(debounceTime(350), distinctUntilChanged(), takeUntilDestroyed(this.destroyRef))
      .subscribe(() => {
        this.page.set(1);
        this.load();
      });
    this.roleFilter.valueChanges.pipe(takeUntilDestroyed(this.destroyRef)).subscribe(() => {
      this.page.set(1);
      this.load();
    });

    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.service
      .list({ search: this.search.value, role: this.roleFilter.value, page: this.page() })
      .subscribe({
        next: (res) => {
          this.users.set(res.data);
          this.currentPage.set(res.meta.current_page);
          this.lastPage.set(res.meta.last_page);
          this.loading.set(false);
        },
        error: () => {
          this.toast.error('No pudimos cargar los usuarios.');
          this.loading.set(false);
        },
      });
  }

  goToPage(page: number): void {
    this.page.set(page);
    this.load();
  }

  doDelete(): void {
    const user = this.toDelete();
    if (!user) return;
    this.service.remove(user.id).subscribe({
      next: () => {
        this.toast.success(`Usuario «${user.name}» eliminado.`);
        this.toDelete.set(null);
        this.load();
      },
      error: (err) => {
        const message =
          (err as { error?: { message?: string } })?.error?.message ?? 'No se pudo eliminar el usuario.';
        this.toast.error(message);
        this.toDelete.set(null);
      },
    });
  }
}
