<?php

if (!function_exists('current_user')) {
    function current_user()
    {
        return session()->get('user');
    }
}

if (!function_exists('user_id')) {
    function user_id()
    {
        return current_user()['id'] ?? null;
    }
}

if (!function_exists('user_username')) {
    function user_username()
    {
        return current_user()['username'] ?? null;
    }
}

if (!function_exists('user_full_name')) {
    function user_full_name()
    {
        return current_user()['full_name'] ?? null;
    }
}

if (!function_exists('user_email')) {
    function user_email()
    {
        return current_user()['email'] ?? null;
    }
}

if (!function_exists('user_role')) {
    function user_role()
    {
        return current_user()['role'] ?? null;
    }
}

if (!function_exists('has_role')) {
    function has_role(string $role)
    {
        return user_role() === $role;
    }
}

if (!function_exists('has_any_role')) {
    function has_any_role(array $roles)
    {
        return in_array(user_role(), $roles, true);
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return session()->has('user');
    }
}

if (!function_exists('logout_user')) {
    function logout_user()
    {
        session()->remove('user');
        session()->destroy();
    }
}
