<?php

namespace App\Support;

/**
 * Static reference lists used in forms. Kept in code (not the database)
 * because they change rarely; curricula proper live in the database (Phase 3).
 */
final class NigeriaReference
{
    public const CLASS_LEVELS = [
        'jss1' => 'JSS 1', 'jss2' => 'JSS 2', 'jss3' => 'JSS 3',
        'ss1' => 'SS 1', 'ss2' => 'SS 2', 'ss3' => 'SS 3',
        'post_secondary' => 'Finished secondary school',
    ];

    public const EXAM_GOALS = [
        'waec' => 'WAEC (WASSCE)',
        'neco' => 'NECO (SSCE)',
        'jamb' => 'JAMB (UTME)',
        'bece' => 'BECE / Junior WAEC',
        'school' => 'School work and class tests',
    ];

    public const STATES = [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
        'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT Abuja', 'Gombe',
        'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
        'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto',
        'Taraba', 'Yobe', 'Zamfara',
    ];
}
