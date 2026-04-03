<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Throwable;

class AppHelper
{
    /**
     * Cleans up technical exception messages for user display.
     *
     * @param string $message
     * @return string
     */
    public static function buildExceptionMessage(string $message): string
    {
        // Check for duplicate key constraint violation (common SQL error)
        if (str_contains($message, 'Duplicate entry') || str_contains($message, '1062')) {
            return "A record with this unique identifier (e.g., ID Number) already exists.";
        }

        // Check for foreign key constraint violation
        if (str_contains($message, 'Cannot delete or update parent row') || str_contains($message, '1451')) {
            return "Cannot perform this action because related records still exist.";
        }

        // Return a generic fallback message for all other errors
        return "An unexpected error occurred. Please try again or contact support.";
    }

    /**
     * Retrieves the current active academic year.
     *
     * @return string
     */
}
