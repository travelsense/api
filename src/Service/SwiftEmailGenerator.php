<?php
namespace Api\Service;

class SwiftEmailGenerator
{
    /**
     * @var string
     */
    private $from_address;

    /**
     * @var string
     */
    private $from_name;

    /**
     * @var array
     */
    private $to;

    public function __construct(string $from_address, string $from_name, array $to)
    {
        $this->from_address = $from_address;
        $this->from_name = $from_name;
        $this->to = $to;
    }

    public function __invoke(string $content, array $records)
    {
        $mes = ($records[0]['context']['exception']) ? (": "
            .get_class($records[0]['context']['exception']).': '
            .$records[0]['context']['exception']->getMessage()
            .' in '.$records[0]['context']['exception']->getFile().':'
            .$records[0]['context']['exception']->getLine()) : '';
        $message = \Swift_Message::newInstance()
            ->setSubject("HopTrip ".$records[0]['level_name'].$mes)
            ->setFrom($this->from_address, $this->from_name)
            ->setTo($this->to);
        return $message;
    }
}
