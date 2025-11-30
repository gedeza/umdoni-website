<?php

namespace Components;

class UserFriendlyErrors
{
    /**
     * Translate AWS Cognito and other technical errors into user-friendly messages
     * @param string $technicalError The raw error message from AWS/system
     * @return string User-friendly error message
     */
    public static function translate($technicalError)
    {
        // Convert to lowercase for easier matching
        $error = strtolower($technicalError);

        // Password validation errors
        if (strpos($error, 'password') !== false) {
            if (strpos($error, 'symbol') !== false) {
                return 'Your password must include at least one special character (e.g., !@#$%^&*)';
            }
            if (strpos($error, 'uppercase') !== false || strpos($error, 'lower') !== false) {
                return 'Your password must contain both uppercase and lowercase letters';
            }
            if (strpos($error, 'numeric') !== false || strpos($error, 'number') !== false) {
                return 'Your password must include at least one number';
            }
            if (strpos($error, 'length') !== false || strpos($error, 'short') !== false) {
                return 'Your password must be at least 8 characters long';
            }
            if (strpos($error, 'did not conform') !== false) {
                return 'Password must be at least 8 characters with uppercase, lowercase, numbers, and special characters (!@#$%^&*)';
            }
        }

        // Username/Email errors
        if (strpos($error, 'usernameexists') !== false || strpos($error, 'already exists') !== false) {
            return 'This email address is already registered. Please login or use a different email';
        }

        if (strpos($error, 'usernotfound') !== false || strpos($error, 'user does not exist') !== false) {
            return 'We couldn\'t find an account with that email address';
        }

        // Verification code errors
        if (strpos($error, 'codemismatch') !== false || strpos($error, 'invalid code') !== false) {
            return 'The verification code you entered is incorrect. Please check and try again';
        }

        if (strpos($error, 'expiredcode') !== false || strpos($error, 'code expired') !== false) {
            return 'Your verification code has expired. Please request a new one';
        }

        // Invalid parameters
        if (strpos($error, 'invalidparameter') !== false) {
            if (strpos($error, 'email') !== false) {
                return 'Please enter a valid email address';
            }
            return 'Please check that all fields are filled in correctly';
        }

        // Network/connection errors
        if (strpos($error, 'network') !== false || strpos($error, 'connection') !== false || strpos($error, 'timeout') !== false) {
            return 'Connection error. Please check your internet connection and try again';
        }

        // Rate limiting
        if (strpos($error, 'limit exceeded') !== false || strpos($error, 'too many') !== false) {
            return 'Too many attempts. Please wait a few minutes before trying again';
        }

        // Default fallback for unknown errors
        return 'Something went wrong. Please try again or contact support if the problem persists';
    }

    /**
     * Translate success messages to be more user-friendly
     * @param string $message The success message
     * @return string User-friendly success message
     */
    public static function translateSuccess($message)
    {
        $msg = strtolower($message);

        if (strpos($msg, 'registration') !== false || strpos($msg, 'registered') !== false) {
            return 'Welcome! Please check your email for a verification code to activate your account';
        }

        if (strpos($msg, 'verified') !== false || strpos($msg, 'verification') !== false) {
            return 'Your account has been verified! You can now login';
        }

        if (strpos($msg, 'password') !== false && strpos($msg, 'reset') !== false) {
            return 'Your password has been reset successfully. You can now login with your new password';
        }

        return $message;
    }
}
