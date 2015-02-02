<?php

Route::any(config('swaggervel.doc-route').'/{page?}', function($page='api-docs.json') {
    $filePath = config('swaggervel.doc-dir') . "/{$page}";

    if (!File::Exists($filePath)) {
        abort(404, "Cannot find {$filePath}");
    }

    $content = json_decode(File::get($filePath), 1);
    // Escaped slash is causing errors on fetching schema
    return json_encode($content, JSON_UNESCAPED_SLASHES);

});

get('api-docs', function() {
    if (config('swaggervel.generateAlways')) {
        $appDir = base_path()."/".config('swaggervel.app-dir');
        $docDir = config('swaggervel.doc-dir');

        if (!File::exists($docDir) || is_writable($docDir)) {
            // delete all existing documentation
            if (File::exists($docDir)) {
                File::deleteDirectory($docDir);
            }

            File::makeDirectory($docDir);

            $basepath       = "";
            $apiVersion     = "";
            $swaggerVersion = "";
            $excludes       = "";

            $defaultBasePath = config('swaggervel.default-base-path');
            if ( ! empty($defaultBasePath)) {
                $basepath .= " --default-base-path '{$defaultBasePath}'";
            }

            $defaultApiVersion = config('swaggervel.default-api-version');
            if ( ! empty($defaultApiVersion)) {
                $apiVersion = " --default-api-version '{$defaultApiVersion}'";
            }

            $defaultSwaggerVersion = config('swaggervel.default-swagger-version');
            if ( ! empty($defaultSwaggerVersion)) {
                $swaggerVersion = " --default-swagger-version '{$defaultSwaggerVersion}'";
            }

            $exludeDirs = config('swaggervel.excludes');
            if (is_array($exludeDirs) && ! empty($exludeDirs)){
                $excludes = " -e " . implode(":", $exludeDirs);
            }

            $cmd = "php " . base_path() . "/vendor/zircote/swagger-php/swagger.phar $appDir -o {$docDir} {$apiVersion} {$swaggerVersion} {$basepath} {$excludes}";

            $result = shell_exec($cmd);

            //display all swagger-php error messages so that it doesn't fail silently
            if ((strpos($result, "[INFO]") != FALSE) || (strpos($result, "[WARN]") != FALSE) || (strpos($result, "[ERROR]") != FALSE)) {
                throw new \Exception($result);
            }
        }
    }

    $response = Response::make(
        view('swaggervel::index', ['urlToDocs' => url(config('swaggervel.doc-route')),
            'requestHeaders' => config('swaggervel.requestHeaders') ]),
        200
    );

    if (Config::has('swaggervel.viewHeaders')) {
        foreach (config('swaggervel.viewHeaders') as $key => $value) {
            $response->header($key, $value);
        }
    }

    return $response;
});