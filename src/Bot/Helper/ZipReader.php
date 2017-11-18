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
        $head = unpack(self::UNZIP_FORMAT, substr($zipContent,0,30));
//        $filename = substr($zipContent,30,$head['namelen']);
        return gzinflate(substr($zipContent,30+$head['namelen']+$head['exlen']));
    }
}