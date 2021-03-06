<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 01.02.17
 * Time: 14:20
 */
class Debug_Tool_Errors
{
    protected static $instance;

    private $errors = array();
    private $real_error_handler = array();

    private function __construct()
    {
        add_filter('wp_debug_refs', array($this, 'add_ref'));

        $this->real_error_handler = set_error_handler(array($this, 'error_handler'));
    }

    function error_handler($type, $message, $file, $line)
    {

        switch ($type) {
            case E_WARNING :
            case E_USER_WARNING :
                $error['type'] = 'Warning';
                break;
            case E_NOTICE :
            case E_USER_NOTICE :
                $error['type'] = 'Notice';
                break;
            case E_STRICT :
                // TODO
                break;
            case E_DEPRECATED :
            case E_USER_DEPRECATED :
                // TODO
                break;
            case 0 :
                // TODO
                break;
        }

        $this->errors[] = array(
            'type' => $type,
            'location' => $file . ':' . $line,
            'message' => $message,
            'stack' => wp_debug_backtrace_summary(__CLASS__)
        );

        if (null != $this->real_error_handler)
            return call_user_func($this->real_error_handler, $type, $message, $file, $line);
        else
            return false;
    }

    public function add_ref($refs)
    {
        if (count($this->errors)) {
            ob_start();

                $errors_str = '';
                $srror_stat = array();

                foreach ($this->errors as $error) {

                    switch ($error['type']) {
                        case E_WARNING :
                        case E_USER_WARNING :
                            $type = 'Warning';
                            $error_class = 'dt-warning';
                            break;
                        case E_NOTICE :
                        case E_USER_NOTICE :
                            $type = 'Notice';
                            $error_class = 'dt-notice';
                            break;
                        case E_STRICT :
                            $type = 'Strict';
                            $error_class = 'dt-strict';
                            break;
                        case E_DEPRECATED :
                        case E_USER_DEPRECATED :
                            $type = 'Deprecated';
                            $error_class = 'dt-deprecated';
                            break;
                        case 0 :
                        default:
                            $type = 'Undefined error';
                            $error_class = 'dt-undefined';
                            break;
                    }
                    ob_start();
                    if (isset($srror_stat[$type]))  $srror_stat[$type]++;
                    else $srror_stat[$type] = 1;
                    ?>
                    <li class="debug-tool-error <?php echo $error_class; ?>">
                        <strong><?php echo $type . ': '; ?></strong>
                        <?php echo str_replace(ABSPATH, '', $error['location']) . ' - ' . strip_tags($error['message']); ?>
                        <br/>
	                    <?php $stacktrace = explode(',', $error['stack']); ?>
                        <ul class="stacktrace">
                        <?php foreach ($stacktrace as $func) {
                            ?><li><?php echo trim($func) ?></li><?php
                        } ?>
                        </ul>
<!--                        <em><?php echo $error['stack']; ?></em>-->
                    </li>
                    <?php
                    $errors_str .= ob_get_clean();
                }

                ?>
            <p>
                <?php
                $i = 0;
                foreach ( $srror_stat as $type => $num ) {
	                $i++;
                    echo (($i !== 1) ? ' | ' : '' ) . $type . ': ' . $num;

                }
                ?>

            </p>

            <ol class="debug-tool-error-list">
	        <?php echo $errors_str; ?>
            </ol>

            <?php


            $refs['errors'] = array(
                'title' => 'Errors' . ' (' . count($this->errors) . ')' ,
                'content' => ob_get_clean(),
            );
        }
        return $refs;
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

add_action('load_debug_tools', array('Debug_Tool_Errors', 'get_instance'));