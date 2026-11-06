<?php

namespace App\Validation;

class CustomRules
{
    public function validateCaptcha(string $str, string &$error = null): bool
    {
        $session = session();
        $captcha = $session->get('captcha');
        
        if ($str === $captcha) {
            return true;
        }
        
        $error = 'Captcha tidak sesuai';
        return false;
    }
}