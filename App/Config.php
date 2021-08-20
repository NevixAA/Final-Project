<?php

namespace App;

/**
 * Application configuration
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';
	
	/**
	 * Database Port
	 * @var string
	 */
	 const DB_PORT = '3306';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'femin';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'ZfmrmqYMbREsm5eLACYeWao6B7NwJpD8';
}
