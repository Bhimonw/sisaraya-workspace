<?php

namespace App\Helpers;

class RoleHelper
{
    /**
     * Get role label
     */
    public static function getLabel(string $roleKey): string
    {
        return config("roles.definitions.{$roleKey}.label", ucfirst($roleKey));
    }

    /**
     * Get role description
     */
    public static function getDescription(string $roleKey): string
    {
        return config("roles.definitions.{$roleKey}.description", '');
    }

    /**
     * Get role color
     */
    public static function getColor(string $roleKey): string
    {
        return config("roles.definitions.{$roleKey}.color", 'gray');
    }

    /**
     * Get badge CSS classes (light variant)
     */
    public static function getBadgeClass(string $roleKey): string
    {
        $color = self::getColor($roleKey);
        return config("roles.badge_classes.{$color}", 'bg-gray-100 text-gray-700 border-gray-200');
    }

    /**
     * Get badge CSS classes (dark variant)
     */
    public static function getBadgeClassDark(string $roleKey): string
    {
        $color = self::getColor($roleKey);
        return config("roles.badge_classes_dark.{$color}", 'bg-gray-500 text-white border-gray-600');
    }

    /**
     * Get all roles with their data
     */
    public static function getAllRoles(): array
    {
        return config('roles.definitions', []);
    }

    /**
     * Get only role labels (for backward compatibility)
     */
    public static function getRoleLabels(): array
    {
        $labels = [];
        foreach (config('roles.definitions', []) as $key => $data) {
            $labels[$key] = $data['label'];
        }
        return $labels;
    }

    /**
     * Get role data for Alpine.js
     */
    public static function getRolesForAlpine(): array
    {
        $roles = [];
        foreach (config('roles.definitions', []) as $key => $data) {
            $roles[] = [
                'value' => $key,
                'label' => $data['label'],
                'color' => config("roles.badge_classes.{$data['color']}", 'bg-gray-100 text-gray-700'),
                'count' => 0, // Will be updated by Alpine.js
            ];
        }
        return $roles;
    }

    /**
     * Render role badge HTML
     */
    public static function badge(string $roleKey, bool $dark = false): string
    {
        $label = self::getLabel($roleKey);
        $classes = $dark ? self::getBadgeClassDark($roleKey) : self::getBadgeClass($roleKey);
        
        return sprintf(
            '<span class="text-xs px-2 py-0.5 rounded-full border font-medium %s">%s</span>',
            $classes,
            htmlspecialchars($label)
        );
    }
}
