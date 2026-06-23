<?php defined('BASEPATH') OR exit('No direct script access allowed');

function asset_url($path = '') { return base_url('assets/'.ltrim($path, '/')); }
function current_user() { return get_instance()->session->userdata('user'); }
function is_logged_in() { return (bool) current_user(); }
function is_admin() { $user = current_user(); return $user && !empty($user['is_admin']); }
function money($amount) { return '₹'.number_format((float) $amount, 0); }
function csrf_meta() {
    $ci = get_instance();
    return '<meta name="csrf-name" content="'.html_escape($ci->security->get_csrf_token_name()).'">'.
        '<meta name="csrf-token" content="'.html_escape($ci->security->get_csrf_hash()).'">';
}
function product_image_url($path, $fallback = 'images/dress-placeholder.svg') {
    return asset_url($path ?: $fallback);
}
