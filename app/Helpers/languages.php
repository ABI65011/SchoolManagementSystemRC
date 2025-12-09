<?php

if (!function_exists('languages')) {
    /**
     * Return an associative array of ISO-639-1/2 => language name
     * (again, trim or extend to taste).
     */
    function languages(): array
    {
        return [
            'eng' => 'English',
            'swa' => 'Swahili',
            'lug' => 'Luganda',
            'run' => 'Kirundi / Rundi',
            'kin' => 'Kinyarwanda',
            'ara' => 'Arabic',
            'fra' => 'French',
            'por' => 'Portuguese',
            'spa' => 'Spanish',
            'zul' => 'Zulu',
            'xho' => 'Xhosa',
            'afr' => 'Afrikaans',
        ];
    }
}
