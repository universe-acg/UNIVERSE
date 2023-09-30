<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */


// src/Exception/BadEmailFormatException.php
    
    namespace App\Exception;
    
    use InvalidArgumentException;
    use Throwable;
    
    class BadEmailFormatException extends InvalidArgumentException
    {
        public function __construct(string $message = 'Invalid email format', int $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }
