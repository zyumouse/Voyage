<?php
function voyage_is_valid_name(string $value): bool
{
    return preg_match('/\A[\p{L}\p{M}][\p{L}\p{M} .\'-]{0,99}\z/u', $value) === 1;
}

function voyage_is_valid_email(string $value): bool
{
    if (strlen($value) > 255) {
        return false;
    }

    $separator = strrpos($value, '@');
    if ($separator === false) {
        return false;
    }

    $domain = substr($value, $separator + 1);
    if (strpos($domain, '.') === false) {
        $value .= '.invalid';
    }

    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

function voyage_is_valid_phone(string $value): bool
{
    return preg_match('/\A\+[0-9]{7,15}\z/', $value) === 1;
}

function voyage_is_valid_password(string $value): bool
{
    return $value !== '' && strlen($value) <= 255 && preg_match('/\s/', $value) !== 1;
}