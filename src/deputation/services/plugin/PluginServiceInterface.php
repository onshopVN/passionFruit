<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\services\plugin;


interface PluginServiceInterface
{
    public function setup();
    public function remove();
    public function update();
    public function enable();
    public function disable();

}