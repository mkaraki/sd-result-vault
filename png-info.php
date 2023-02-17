<?php
require 'vendor/autoload.php';

use PNGMetadata\PNGMetadata;

function parseDiffusionParameters(string $path)
{

    $png_metadata = new PNGMetadata($path);

    $params = $png_metadata->get('parameters');

    if ($params === NULL || !isset($params['']))
        die('Unable to get parameters');

    $params = $params[''];

    $prms = explode("\n", $params);
    $res = ['others' => ''];
    $res['prompt'] = $prms[0];
    $last_obj = 'prompt';
    for ($i = 1; $i < count($prms); $i++) {
        $prm = $prms[$i];
        if (str_starts_with($prm, 'Negative prompt: ')) {
            $res['negative'] = substr($prm, 17);
            $last_obj = 'negative';
        } elseif (str_starts_with($prm, 'Steps: ')) {
            $ccut = explode(',', $prm);
            $res['generation'] = [];
            $last_sn = '';
            for ($j = 0; $j < count($ccut); $j++) {
                $clncut = explode(': ', $ccut[$j]);
                if (count($clncut) < 2) {
                    $res['generation'][$last_sn] .= ', ' . $ccut[$j];
                    break;
                }
                $sn = $last_sn = trim($clncut[0]);
                $res['generation'][$sn] = trim($clncut[1]);
            }
            $last_obj = 'others';
        } else {
            $res[$last_obj] .= "\n" . $prm;
        }
    }

    if (empty($res['others']))
        unset($res['others']);

    return $res;
}
