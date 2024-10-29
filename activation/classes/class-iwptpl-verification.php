<?php

class IWPTPL_Verification
{
    public static function is_active()
    {
        if (self::isAllowedDomain()) {
            return 'yes';
        }

        $is_active = get_option('iwptpl_is_active', 'no');
        return ($is_active == 'yes' || $is_active == 'skipped');
    }

    public static function skipped()
    {
        $skipped = get_option('iwptpl_is_active', 'no');
        return $skipped == 'skipped';
    }

    private static function isAllowedDomain()
    {
        return (in_array($_SERVER['SERVER_NAME'], [
            // 'localhost',
            'alldemos.space',
            'demos.ithemelandco.com',
            'wordpress.local',
        ]));
    }
}
