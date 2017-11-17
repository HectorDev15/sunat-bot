<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 17/11/2017
 * Time: 06:07 PM
 */

namespace Sunat\Bot\Helper;

/**
 * Class ZipReader
 * @package Sunat\Bot\Helper
 */
final class ZipReader
{
    const UNZIP_FORMAT = 'Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen';
    /**
     * Retorna el contenido del primer xml dentro del zip.
     *
     * @param string $zipContent
     * @return string
     */
    public function decompressXmlFile($zipContent)
    {
        $start = 0;
        $max = 5;
        while ($max > 0) {
            $dat = substr($zipContent, $start, 30);
            if (empty($dat)) {
                break;
            }
            $head = unpack(self::UNZIP_FORMAT, $dat);
            $filename = substr(substr($zipContent, $start),30, $head['namelen']);
            if (empty($filename)) {
                break;
            }
            $count = 30 + $head['namelen'] + $head['exlen'];
            if (strtolower($this->getFileExtension($filename)) == 'xml') {
                echo 'xml: ' . $filename;
                return gzinflate(substr($zipContent, $start + $count, $head['csize']));
            }
            $start += $count + $head['csize'];
            $max--;
        }
        return '';
    }

    function getFileExtension($filename)
    {
        $lastDotPos = strrpos($filename, '.');
        if (!$lastDotPos) return '';
        return substr($filename, $lastDotPos + 1);
    }
}