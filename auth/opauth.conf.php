<?php
/**
 * Opauth basic configuration file to quickly get you started
 * ==========================================================
 * To use: rename to opauth.conf.php and tweak as you like
 * If you require advanced configuration options, refer to opauth.conf.php.advanced
 */

$config = array(
/**
 * Path where Opauth is accessed.
 *  - Begins and ends with /
 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
 *  - if Opauth is reached via http://auth.example.org/, path is '/'
 */
	'path' => '/auth/',

/**
 * Callback URL: redirected to after authentication, successful or otherwise
 */
	'callback_url' => 'http://cachnauanngon.com/auth/callback.php',
	
/**
 * A random string used for signing of $auth response.
 * 
 * NOTE: PLEASE CHANGE THIS INTO SOME OTHER RANDOM STRING
 */
	'security_salt' => 'LDFmiilYf8Fyw5W10rx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4n1',
		
/**
 * Strategy
 * Refer to individual strategy's documentation on configuration requirements.
 * 
 * eg.
 * 'Strategy' => array(
 * 
 *   'Facebook' => array(
 *      'app_id' => 'APP ID',
 *      'app_secret' => 'APP_SECRET'
 *    ),
 * 
 * )
 *
 */
	'Strategy' => array(
		// Define strategies and their respective configs here
		
		'Facebook' => array(
			'app_id' => '552042118236053',
			'app_secret' => '2648cf71d6c5079b7ad7a73e95e1720b',
                        'scope' => 'email',
		),
		
		'Google' => array(
			'client_id' => '555443054507-muamegigkm6qbnhh0pcfv1dkqn0lgp07.apps.googleusercontent.com',
			'client_secret' => 'hof64_FexpMbnQmNmM6kq9D5'
		),
		
		'Twitter' => array(
			'key' => 'H2uUCb89xsfbVZyJi1rQhgIOK',
			'secret' => 'PTxWZwZ7WMBOjwZMShOK1Ww51hHHDGYmrUWluFxBfMpLyRHeUv'
		),
            
                'Yahoo' => array(
			'key' => 'dj0yJmk9Zml5N2lYRHdnZEFqJmQ9WVdrOVl6RTVOWFpITjJzbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1mZQ--',
			'secret' => '2f2767f2c169de0b50404c3b6c5fbac14433f8b3'
		),
				
	),
);