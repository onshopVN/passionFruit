<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\services\plugin;


interface PluginFolderInterface
{
    public function checkStructure();
    public function compress();
    public function removeCompress();
    public function getName();
    public function getCode();
    public function getVersion();
    public function getPriority();
    public function moveTo();
    public function getFolderPlugin();
    public function getFolderAssets();
    public function getFolderTemplate();
    public function getFolderConfig();

}