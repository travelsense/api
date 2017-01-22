<?php
namespace Api\Service;

/**
 * Mutes warning/notices on certain calls
 * @see https://github.com/dompdf/dompdf/issues/1272
 */
class SilentMPDF extends \mPDF
{
    public function WriteHTML($html, $sub = 0, $init = true, $close = true)
    {
        return @parent::WriteHTML($html, $sub, $init, $close);
    }
}
