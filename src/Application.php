<?php
class Application extends Silex\Application
{
    public function __construct(array $config = array())
    {
        $config = array_merge(
            [
                'secure_json' => false,
                'services' => [],
            ],
            $config
        );

        // load secure config
        $secureConfig = $config['secure_json'];
        if ($secureConfig) {
            $config = array_replace_recursive(
                $config,
                json_decode(file_get_contents($secureConfig), true));
        }

        parent::__construct($config);

        //load services
        foreach ($this['services'] as $serviceLoader) {
            require $serviceLoader;
        }
    }
}