Provides assets packaging and compression.

## Features

- package your assets to bundles
- compress your assets

## TODO

- add support for wildcards e.g. bundles/bundle/js/core/*
- add support for nested packages
- create packages on the fly in templates
- add compilers for *.less and other third party compilers

## Installation

### Add Tecbot\AssetPackagerBundle to your src/Bundle dir

    git submodule add git://github.com/tecbot/AssetPackagerBundle.git src/Bundle/Tecbot/AssetPackagerBundle
    
### Add TecbotAssetPackagerBundle to your application Kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            //..
            new Bundle\Tecbot\AssetPackagerBundle\TecbotAssetPackagerBundle(),
            //..
        );
    }
    
### Update your config

#### Add routing

    # app/config/routing.yml
    _assetpackager:
        resource: Tecbot/AssetPackagerBundle/Resources/config/routing.yml
        prefix:   /_ap

#### Add packages

    # app/config/config.yml
    assetpackager.config:
        js: # Javascript Config
            packages: # Javascript Packages
                pack1: # Package Name
                    - bundles/bundle/js/pack1.js
                    - bundles/bundle/js/pack1_1.js
        css: # Stylesheet Config
            packages: # Stylesheet Packages
                pack1: # Package Name
                    - bundles/bundle/css/pack1.css
                    - bundles/bundle/css/pack1_1.css

#### Advanced config

    # app/config/config.yml
    assetpackager.config:
        assets_path: String # Default to %kernel.root_dir%/../web
        cache_path: String # Default to %kernel.cache_dir%/assetpackager
        compress_assets: Boolean # Defaults to true. When false, JavaScript and CSS packages will be left uncompressed. Disabling compression is only recommended if you're packaging assets in development.
        package_assets: Boolean # Defaults to true, packaging and caching assets.
        js: # Javascript Config
            compressor: jsmin, packer, yui, closure or service_id # Defaults to jsmin.
            # OR
            compressor:
                id: jsmin, packer, yui, closure or service_id # Defaults to jsmin
                options: ~ # compressor options. See compressor section.
            # OR
            compressor:
               - # override default compressor options. See compressor section.
            packages: # Javascript Packages
                pack1: # Package Name
                    compressor: # override default compressor options. See above.
                    output: String # Output name for the cache file. Defaults a md5 hash.
                    options: ~ # override global options. e.g. compress_assets
                    paths:
                        - bundles/bundle/js/pack1.js
                        - bundles/bundle/js/pack1_1.js
        css: # Stylesheet Config
            # javascript like. available compiler: cssmin, yui # Defaults to cssmin

## Use

### Twig

    # Add packages to helper
    {{ assetpackage('pack1', 'js') }} # add javascript package
    {{ assetpackage(['pack1', 'pack2', 'pack3'], 'js') }} # add multiple javascript packages

    {{ assetpackage('pack1', 'css') }} # add stylesheet package
    {{ assetpackage(['pack1', 'pack2', 'pack3'], 'css') }} # add multiple stylesheet packages

    # render packages
    {{ assetpackages() }} # render all packages you added to the helper
    {{ assetpackages('css') }} # render only stylesheet packages
    {{ assetpackages('js') }} # render only javascript packages


### PHP
    
    # Add packages to helper
    <?php $view['assetpackager']->add('pack1', 'js'); ?> # add javascript package
    <?php $view['assetpackager']->add(array('pack1', 'pack2', 'pack3'), 'js'); ?> # add multiple javascript packages

    <?php $view['assetpackager']->add('pack1', 'css'); ?> # add stylesheet package
    <?php $view['assetpackager']->add(array('pack1', 'pack2', 'pack3'), 'css'); ?> # add multiple stylesheet packages

    # render packages
    <?php echo $view['assetpackager']->render(); ?> # render all packages you added to the helper
    <?php echo $view['assetpackager']->render('css'); ?> # render only stylesheet packages
    <?php echo $view['assetpackager']->render('js'); ?> # render only javascript packages

## Compressors Options

### Javascript compressors

#### JSMin

No option available

#### JavascriptPacker
    options:
        encoding:       None, Numeric, Normal, High ASCII # Defaults to Normal
        fast_decode:    Boolean # Defaults to true
        special_chars:  Boolean # Defaults to false

#### YUICompressor

    options:
        charset:                String # Defaults to utf-8
        line_break:             Number # Defaults to 5000  
        munge:                  Boolean # Defaults to true
        optimize:               Boolean # Defaults to true
        preserve_semicolons:    Boolean # Defaults to false
        path:                   String # Path of the yuicompressor.jar

#### Google Closure Compiler

    options:
        compilation_level:  WHITESPACE_ONLY, SIMPLE_OPTIMIZATIONS, ADVANCED_OPTIMIZATIONS # Defaults to SIMPLE_OPTIMIZATIONS
        path:               String # Path of the closure-compiler.jar

### Stylesheet compressors

#### CSSMin

    options:
        remove-empty-blocks:     Boolean # Defaults to true
        remove-empty-rulesets:   Boolean # Defaults to true
        remove-last-semicolons:  Boolean # Defaults to true
        convert-css3-properties: Boolean # Defaults to false
        convert-color-values:    Boolean # Defaults to false
        compress-color-values:   Boolean # Defaults to false
        compress-unit-values:    Boolean # Defaults to false
        emulate-css3-variables:  Boolean # Defaults to true

#### YUICompressor

    options:
        charset:                String # Defaults to utf-8
        line_break:             Number # Defaults to 0
        path:                   String # Path of the yuicompressor.jar

## Command lines

### Clear the generated cache files

    console assetpackager:clear-cache

### Compress all packages

    console assetpackager:compress-packages
