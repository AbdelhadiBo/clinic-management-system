<?php

if (!function_exists('request_parse_body')) {
    function request_parse_body(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            return [json_decode($input, true) ?? [], []];
        }

        if (strpos($contentType, 'multipart/form-data') !== false) {
            return [$_POST, $_FILES];
        }

        parse_str(file_get_contents('php://input'), $data);
        return [$data, []];
    }
}
