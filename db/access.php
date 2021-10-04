<?php
defined('MOODLE_INTERNAL') || die();

    $capabilities = array(

    'local/th_dashboard:viewthdashboard' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW,
        ),

        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    )
);