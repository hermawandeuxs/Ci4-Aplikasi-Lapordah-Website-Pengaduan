<?php

if (!function_exists('get_report_image')) {
    function get_report_image($image_name) {
        $default_image = 'assets/img/no-image.png';
        
        // If image name is empty, return default
        if (empty($image_name)) {
            return base_url($default_image);
        }

        $image_path = FCPATH . 'uploads/reports/' . $image_name;
        
        // Check if file exists and is readable
        if (!file_exists($image_path) || !is_readable($image_path)) {
            return base_url($default_image);
        }


 return base_url('uploads/reports/' . $image_name);

   }
}

if (!function_exists('get_status_color')) {
    function get_status_color($status) {
        switch (strtolower($status)) {
            case 'pending':
                return 'warning';
            case 'verified':
                return 'success';
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}