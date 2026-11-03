<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Если запрос ожидает JSON — не редиректим
        if ($request->expectsJson()) {
            return null;
        }

        // Если это страница входа или регистрации — пропускаем
        if ($request->routeIs('login') || $request->routeIs('register')) {
            return null;
        }

        // Если пользователь не авторизован и пытается зайти на защищённую страницу
        // Редиректим на страницу входа
        return route('login');
    }
}