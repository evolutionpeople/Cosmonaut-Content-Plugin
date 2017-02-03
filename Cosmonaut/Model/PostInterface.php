<?php
/**
 * Created by PhpStorm.
 * User: francescocolonna
 * Date: 01/12/16
 * Time: 09:56
 */
namespace Cosmonaut\Model;
interface PostInterface {

    public function withDetails();

    public function thumbnail();
}