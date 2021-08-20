<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Mailer configuration
 */
class ConfigMailer
{

    /**
     * Enable verbose debug output
     * @var int
     */
    const SMTPDebug = SMTP::DEBUG_SERVER;

    /**
     * Send using SMTP
     * @var boolean
     */
    const isSMTP = true;

    /**
     * Set the SMTP server to send through
     * @var string
     */
    const Host = 'smtp.gmail.com';

    /**
     *  Enable SMTP authentication
     * @var boolean
     */
    const SMTPAuth = true;

    /**
     * SMTP username
     * @var boolean
     */
    const Username = 'feminproject@gmail.com';

    /**
     * SMTP password
     * @var string
     */
    const Password = 'yuval10nevo';

    /**
     * Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
     * @var string
     */
    const SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    /**
     * TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
     * @var int
     */
    const Port = 587;
}
