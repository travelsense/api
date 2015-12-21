<?php
class Application extends Silex\Application
{
    public function __construct(array $config = [])
    {
        $config = array_merge(
            [
                'secure_json' => false,
                'service' => [],
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

        parent::__construct(['config' => $config]);

        //load service
        $app = $this; // used in includes
        foreach ($config['service'] as $serviceLoader) {
            require $serviceLoader;
        }
    }

    /**
     * @param string $env
     * @return Application
     */
    static public function createByEnvironment($env)
    {
        return new self(require sprintf(__DIR__.'/../app/config/%s.php', $env));
    }
}