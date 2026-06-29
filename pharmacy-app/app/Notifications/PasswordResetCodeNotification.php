<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Envía el código de recuperación de contraseña por correo.
 *
 * En local el mailer es `log`: el código queda en storage/logs/laravel.log.
 * En producción configurar un mailer SMTP real.
 */
class PasswordResetCodeNotification extends Notification
{
    public function __construct(
        public readonly string $code,
        public readonly int $ttlMinutes,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = trim((string) ($notifiable->name ?? ''));

        return (new MailMessage)
            ->subject('Código de recuperación · Farmacia Hedman & Garcia')
            ->greeting($name !== '' ? "Hola {$name}," : 'Hola,')
            ->line('Usa este código para restablecer tu contraseña:')
            ->line('**'.$this->code.'**')
            ->line("El código vence en {$this->ttlMinutes} minutos y solo puede usarse una vez.")
            ->line('Si no solicitaste el cambio, ignora este mensaje: tu contraseña sigue protegida.');
    }
}
