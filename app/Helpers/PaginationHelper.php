<?php

namespace App\Helpers;

class PaginationHelper
{
    /**
     * Get current page from various URL formats
     */
    public static function getCurrentPage($request)
    {
        // First check query parameter
        $page = (int)$request->getGet('page');
        if ($page > 0) {
            return $page;
        }

        // Then check URI segment
        $segment = $request->getUri()->getSegment(2);
        if (is_numeric($segment)) {
            return (int)$segment;
        }

        // Default to page 1
        return 1;
    }


    public static function getQueryString($filters)
    {
        $query = http_build_query(array_filter($filters, fn($v) => $v !== null && $v !== '' && $v !== 'all' && $v !== 'newest'));
        return $query ? '&' . $query : '';
    }

}