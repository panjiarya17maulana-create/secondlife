<?php

// Laravel Helper Functions Stubs for IDE
// This file provides IDE autocomplete for Laravel helper functions

if (!function_exists('view')) {
    /**
     * Get the evaluated view contents of the given view.
     * @param string|null $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory|void
     */
    function view($view = null, $data = [], $mergeData = [])
    {
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a new redirect response.
     * @param string|null $to
     * @param int $status
     * @param array $headers
     * @param bool|null $secure
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
    }
}

if (!function_exists('back')) {
    /**
     * Create a new redirect response to the previous location.
     * @param int $status
     * @param array $headers
     * @param mixed $fallback
     * @return \Illuminate\Http\RedirectResponse
     */
    function back($status = 302, $headers = [], $fallback = false)
    {
    }
}

if (!function_exists('now')) {
    /**
     * Create a new Carbon instance for the current time.
     * @param string|\DateTimeZone|null $tz
     * @return \Illuminate\Support\Carbon
     */
    function now($tz = null)
    {
    }
}

if (!function_exists('auth')) {
    /**
     * Get the authentication guard.
     * @param string|null $guard
     * @return \Illuminate\Auth\AuthManager|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    function auth($guard = null)
    {
    }
}
