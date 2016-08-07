<?php
namespace Api\Test;

use Api\Application;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    use PHPServerTrait;
    use DatabaseTrait;

    /**
     * @var ApiClient
     */
    protected $client;
    
    const SERVER_LOG            = '/tmp/php-server.log';
    const TAIL_NUM_LINES        = 10;
    const INTERNAL_SERVER_ERROR = 'Internal Server Error';

    public static function setUpBeforeClass()
    {
        self::startServer(self::SERVER_LOG);
    }

    public static function tearDownAfterClass()
    {
        self::stopServer();
    }
    
    public function setUp()
    {
        $app = Application::createByEnvironment();
        $env = $app['env'];
        if ($env !== 'test') {
            $this->markTestSkipped("Functional tests are disabled on this environment: $env");
        }
        $this->resetDatabase($app);
        $this->client = new ApiClient(sprintf('%s:%s', self::$host, self::$port));
    }

    /**
     * Creates a user and logs him in
     * @param string $email
     */
    protected function createAndLoginUser($email = 'sasha@pushkin.ru')
    {
        $password = '123';
        $this->client->registerUser([
            'firstName' => 'Alexander',
            'lastName'  => 'Pushkin',
            'picture'   => 'http://pushkin.ru/sasha.jpg',
            'email'     => $email,
            'password'  => $password,
            'creator'   => true,
        ]);
        $token = $this->client->getTokenByEmail($email, $password);
        $this->client->setAuthToken($token);
    }

    /**
     * Tail server log file defined in SERVER_LOG
     * @param int $nun_lines
     * @return string
     */
    public static function tailServerLog(int $nun_lines = self::TAIL_NUM_LINES)
    {
        $output = "";
        if(file_exists(self::SERVER_LOG)) {
            $output .= "\n" . shell_exec('exec tail -n' . $nun_lines . ' ' . self::SERVER_LOG);
        }
        return $output;
    }
}
