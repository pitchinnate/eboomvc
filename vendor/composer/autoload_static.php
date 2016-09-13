<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7e72d5a40034996561721504974f4f0b
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'Eboo\\' => 5,
        ),
        'D' => 
        array (
            'Dotenv\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Eboo\\' => 
        array (
            0 => __DIR__ . '/../..' . '/framework/Eboo',
        ),
        'Dotenv\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7e72d5a40034996561721504974f4f0b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7e72d5a40034996561721504974f4f0b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
