<?php

/**
 * Methods for determining the details of the very initial environment,
 * prior to the rest of the system coming up
 */
class BootstrapConfig
{

    /**
     * The PHP error reporting level.
     */
    const ERROR_REPORTING = IN_PRODUCTION
        ? E_ALL ^ E_NOTICE
        : E_ALL;


    /**
     * The PHP timezone will be set to this value using date_default_timezone_set
     *
     * If set to an empty value then the timzone will not be set, which may cause
     * warnings if the server config has not set the timezone.
     */
    const TIMEZONE = 'Australia/Adelaide';


    /**
     * Turns on the debug mode for origin cleanup, which outputs the redirect
     * which would occur, but doesn't actually perform the redirect
     */
    const ORIGIN_CLEANUP_DEBUG = false;


    /**
     * Whether to process fatal errors with the shutdown handler.
     *
     * Else uses native display_errors.
     */
    const ENABLE_FATAL_ERRORS = true;


    /**
     * Toggle Kohana caching for files and configurations.
     */
    const ENABLE_KOHANA_CACHE = false;


    /**
     * Copy media assets into the target web folder.
     *
     * This assumes the web server (nginx/apache) will pick up the real file
     * before it defers to the PHP application.
     */
    const ENABLE_MEDIA_CACHE = IN_PRODUCTION;


    /**
     * Specify what the protocol and/or hostname which should be for requests
     * If this doesn't match the current values, then a 301 redirect will occur
     *
     * Default version of this method does nothing, but commented-out examples
     * for common adjustments are included
     *
     * @param string $proto Current request protocol, either 'http' or 'https'
     * @param string $hostname Current request hostname, e.g. 'example.com'
     * @return array New values for $proto and $hostname vars.
     *      If these are different from the incoming values, then a redirect will occur
     *      First element is protocol, second element is hostname
     *      Example: ['https', 'www.example.com']
     */
    public static function originCleanup($proto, $hostname)
    {
        // On test-server, don't change anything
        if (!IN_PRODUCTION) {
            return [$proto, $hostname];
        }

        // Force https for all traffic
        ////$proto = 'https';

        // Force specific domain name
        ////$hostname = 'www.example.com';

        // If hostname does not begin with www. then append this
        ////if (strpos($hostname, 'www.') !== 0) {
        ////    $hostname = 'www.' . $hostname;
        ////}

        // If hostname begins with www. then strip this off
        ////if (strpos($hostname, 'www.') === 0) {
        ////    $hostname = substr($hostname, 4);
        ////}

        return [$proto, $hostname];
    }

}
