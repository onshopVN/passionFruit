<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\services\plugin;


interface PluginPackageInterface
{
    public function checkFile();
    public function decompress();
    public function removeDecompress();
    public function moveTo();
    public function remove();

}