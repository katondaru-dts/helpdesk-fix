<?php

/**
 * Text CAPTCHA Helper
 * Generates a random alphanumeric string.
 */

if (!function_exists('generate_captcha')) {
    /**
     * Generate a new alphanumeric CAPTCHA.
     * Stores the answer in session and returns the string.
     */
    function generate_captcha(int $length = 6): string
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Removed similar looking characters like 0, O, 1, I
        $captcha = '';
        for ($i = 0; $i < $length; $i++) {
            $captcha .= $characters[random_int(0, strlen($characters) - 1)];
        }

        session()->set('captcha_answer', $captcha);
        session()->set('captcha_question', $captcha);

        return $captcha;
    }
}

if (!function_exists('verify_captcha')) {
    /**
     * Verify user input against the stored captcha answer.
     */
    function verify_captcha(string $input): bool
    {
        $answer = session()->get('captcha_answer');
        if ($answer === null) {
            return false;
        }
        return strtoupper(trim($input)) === $answer;
    }
}

if (!function_exists('clear_captcha')) {
    /**
     * Remove CAPTCHA data from session.
     */
    function clear_captcha(): void
    {
        session()->remove('captcha_answer');
        session()->remove('captcha_question');
        session()->remove('captcha_required');
    }
}
