<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dezzpil
 * Date: 1/14/14
 * Time: 2:45 AM
 * To change this template use File | Settings | File Templates.
 */

namespace msmvc\core;

class viewException extends \Exception {}

class noViewZoneException extends viewException {}

class emptyViewZoneException extends viewException {}

class noViewTemplateException extends viewException {}

class emptyViewTemplateException extends viewException {}