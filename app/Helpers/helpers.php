<?php

use App\Helpers\RoleHelper;

if (!function_exists('role_label')) {
    /**
     * Get role label
     * 
     * @param string $roleKey
     * @return string
     */
    function role_label(string $roleKey): string
    {
        return RoleHelper::getLabel($roleKey);
    }
}

if (!function_exists('role_badge')) {
    /**
     * Render role badge HTML
     * 
     * @param string $roleKey
     * @param bool $dark
     * @return string
     */
    function role_badge(string $roleKey, bool $dark = false): string
    {
        return RoleHelper::badge($roleKey, $dark);
    }
}

if (!function_exists('role_badge_class')) {
    /**
     * Get role badge CSS classes
     * 
     * @param string $roleKey
     * @param bool $dark
     * @return string
     */
    function role_badge_class(string $roleKey, bool $dark = false): string
    {
        return $dark ? RoleHelper::getBadgeClassDark($roleKey) : RoleHelper::getBadgeClass($roleKey);
    }
}

if (!function_exists('all_roles')) {
    /**
     * Get all roles with their data
     * 
     * @return array
     */
    function all_roles(): array
    {
        return RoleHelper::getAllRoles();
    }
}

if (!function_exists('role_labels')) {
    /**
     * Get all role labels (backward compatibility)
     * 
     * @return array
     */
    function role_labels(): array
    {
        return RoleHelper::getRoleLabels();
    }
}
