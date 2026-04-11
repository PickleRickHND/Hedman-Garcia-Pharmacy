<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Rutas exentas del checkeo (para no crear loop de redirect).
     *
     * @var array<int, string>
     */
    protected array $allowed = [
        'password.change-required',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password && ! $request->routeIs($this->allowed)) {
            return redirect()->route('password.change-required');
        }

        return $next($request);
    }
}
