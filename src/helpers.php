<?php

if (!function_exists('updateEnv')) {
    function updateEnv($data = [])
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // $envContent = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $envContent);
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                // $envContent .= PHP_EOL . "{$key}=\"{$value}\"";
                $envContent .= PHP_EOL . "{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
