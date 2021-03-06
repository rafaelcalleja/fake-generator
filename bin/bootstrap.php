<?php

/*
 * This file is part of the faker generator package.
 *
 * (c) Rafael Calleja <rafaelcalleja@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$file = '/../vendor/kendall-hopkins/formal-theory/lib/FormalTheory/Autoload.php';

if( file_exists(__DIR__.$file) !== false )
{
    require_once __DIR__.$file;
}else
{
    require_once __DIR__.'/../../../'.$file;
}


FormalTheory_Autoload::register();
