<?php

Route::any(Config::get('swaggervel::app.doc-route').'/{page?}', function($page='api-docs.json') {
    $filePath = Config::get('swaggervel::app.doc-dir') . "/{$page}";

    if (!File::Exists($filePath)) {
        abort(404, "Cannot find {$filePath}");
    }

    $content = json_decode(File::get($filePath), 1);
    // Escaped slash is causing errors on fetching schema
    return json_encode($content, JSON_UNESCAPED_SLASHES);

});

get('api-docs', function() {
    if (Config::get('swaggervel::app.generateAlways')) {
        $appDir = base_path()."/".Config::get('swaggervel::app.app-dir');
        $docDir = Config::get('swaggervel::app.doc-dir');

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

            $defaultBasePath = Config::get('swaggervel::app.default-base-path');
            if ( ! empty($defaultBasePath)) {
                $basepath .= " --default-base-path '{$defaultBasePath}'";
            }

            $defaultApiVersion = Config::get('swaggervel::app.default-api-version');
            if ( ! empty($defaultApiVersion)) {
                $apiVersion = " --default-api-version '{$defaultApiVersion}'";
            }

            $defaultSwaggerVersion = Config::get('swaggervel::app.default-swagger-version');
            if ( ! empty($defaultSwaggerVersion)) {
                $swaggerVersion = " --default-swagger-version '{$defaultSwaggerVersion}'";
            }

            $exludeDirs = Config::get('swaggervel::app.excludes');
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
        View::make('swaggervel::index', array('urlToDocs' => url(Config::get('swaggervel::app.doc-route')), 'requestHeaders' => Config::get('swaggervel::app.requestHeaders') )),
        200
    );

    if (Config::has('swaggervel::app.viewHeaders')) {
        foreach (Config::get('swaggervel::app.viewHeaders') as $key => $value) {
            $response->header($key, $value);
        }
    }

    return $response;
});
