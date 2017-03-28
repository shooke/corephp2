<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-2-20
 * Time: 上午10:50
 */

namespace corephp\base;


class Event
{
    const FRAMEWORK_START = 'framework_start';
    const FRAMEWORK_END = 'framework_end';

    const BEFORE_ROUTE = 'before_route';
    const AFTER_ROUTE = 'after_route';

    const BEFORE_CONTROLLER = 'before_controller';
    const AFTER_CONTROLLER = 'after_controller';

    const BEFORE_ACTION = 'before_action';
    const AFTER_ACTION = 'after_action';

}