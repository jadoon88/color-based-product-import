<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit66905b56d51c7ba4f45067ee0990aef6
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MerryCode\\ColorBasedProductImport\\' => 34,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MerryCode\\ColorBasedProductImport\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit66905b56d51c7ba4f45067ee0990aef6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit66905b56d51c7ba4f45067ee0990aef6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit66905b56d51c7ba4f45067ee0990aef6::$classMap;

        }, null, ClassLoader::class);
    }
}
