<?php
namespace pte\compiler;

/**
 * Class FruitPresent
 * @package pte\compiler
 *
 * presenting fruit to PHP language
 */
class FruitPresent
{

    var $structure = array(
        'FILE' => '<?php %s ?>', //ok
        'VARIABLE' => '$%s;', //ok
        'VARIABLE_VALUE' => '$%s = %s;', //ok
        'VARIABLE_OBJECT' => '$%s = new %s();', //ok
        'FOREACH' => 'foreach (%s as %s) { %s }', //ok
        'FOREACH_KEY_VAL' => 'foreach (%s as %s => %s) { %s }', //ok
        'WHILE' => 'while (%s %s %s) { %s }', //ok
        'DO_WHILE' => 'do { %s } while (%s %s %s)', //ok
        'IF' => 'if (%s %s %s) (%s)', //ok
        'IF_ELSE' => 'if (%s %s %s) ( %s ) else { %s }', //ok
        'FUNCTION_PUBLIC' => 'public function %s(%s) { %s }', //ok
        'FUNCTION_PRIVATE' => 'private function %s(%s) { %s }', //ok
        'FUNCTION_STATIC' => 'static function %s(%s) { %s }', //ok
        'CLASS' => 'class %s { %s }', //ok
        'CLASS_EXTENDS' => 'class %s extends %s { %s } ', //ok
        'CLASS_IMPLEMENTS' => 'class %s implements %s { %s }', //ok
        'CLASS_EXTENDS_IMPLEMENTS' => 'class %s extends %s implements %s { %s }', //ok
        'NAMESPACE' => 'namespace %s;',//ok
        'LOGIC' => '(%s %s %s) ? %s : %s;', //ok
        'CASTING' => '(%s)', //ok
        'SWITCH' => 'SWITCH (%s) %s', //ok
    );

    var $data = array(
        'INTEGER' => 'int',
        'DOUBLE' => 'double',
        'STRING' => 'string',
    );

    var $operator = array(
        'PLUS' => '+',
        'MINUS' => '-',
        'MULTIPLY' => '*',
        'DIVIDE' => '/',
        'POW' => '^',
        'GREATER' => '>',
        'GREATER_EQUAL' => '>=',
        'LOWER' => '<',
        'LOWER_EQUAL' => '<=',
        'EQUAL' => '==',
        'EQUAL_STRICT' => '===',
        'INSTANCEOF' => 'instanceof',
        'CONNECTION' => '.',
    );

    var $commands = array(
        'ASSIGN' => '=',
        'BREAK' => 'break;',
        'RETURN' => 'return %s',
        'CASE' => 'CASE %s:',
        'EXIT' => 'exit();',
        'DIE' => 'die();',
    );

    function structure_file($inner_data)
    {
        return $this->compile($this->structure['FILE'], $inner_data);
    }

    function structure_class($name, $content)
    {
        return $this->compile($this->structure['CLASS'], $name, $content);
    }

    function structure_class_extends($name, $extends, $content)
    {
        return $this->compile($this->structure['CLASS_EXTENDS'], $name, $extends, $content);
    }

    function structure_class_implements($name, $implements, $content)
    {
        return $this->compile($this->structure['CLASS_IMPLEMENTS'], $name, $implements, $content);
    }

    function structure_variable($inner_data)
    {
        return $this->compile($this->structure['VARIABLE'], $inner_data);
    }

    function structure_variable_value($name, $value)
    {
        return $this->compile($this->structure['VARIABLE_VALUE'], $name, $value);
    }

    function structure_variable_object($name, $object)
    {
        return $this->compile($this->structure['VARIABLE_OBJECT'], $name, $object);
    }

    function structure_foreach($array, $var_val, $for_body)
    {
        return $this->compile($this->structure['FOREACH'], $array, $var_val, $for_body);
    }

    function structure_foreach_key_val($array, $var_key, $var_val, $for_body)
    {
        return $this->compile($this->structure['FOREACH_KEY_VAL'], $array, $var_key, $var_val, $for_body);
    }

    function structure_while($variable, $condition, $comparison, $while_body)
    {
        return $this->compile($this->structure['WHILE'], $variable, $condition, $comparison, $while_body);
    }

    function structure_do_while($do_while_body, $variable, $condition, $comparison)
    {
        return $this->compile($this->structure['DO_WHILE'], $do_while_body, $variable, $condition, $comparison);
    }

    function structure_if($variable, $condition, $comparison, $if_body)
    {
        return $this->compile($this->structure['IF'], $variable, $condition, $comparison, $if_body);
    }

    function structure_if_else($variable, $condition, $comparison, $if_body, $else_body)
    {
        return $this->compile($this->structure['IF_ELSE'], $variable, $condition, $comparison, $if_body, $else_body);
    }

    function structure_function_public($function, $parameter, $body)
    {
        return $this->compile($this->structure['FUNCTION_PUBLIC'], $function, $parameter, $body);
    }

    function structure_function_private($function, $parameter, $body)
    {
        return $this->compile($this->structure['FUNCTION_PRIVATE'], $function, $parameter, $body);
    }

    function structure_function_static($function, $parameter, $body)
    {
        return $this->compile($this->structure['FUNCTION_STATIC'], $function, $parameter, $body);
    }

    function structure_class_extends_implements($class, $extends, $implements, $class_body)
    {
        return $this->compile($this->structure['CLASS_EXTENDS_IMPLEMENTS'], $class, $extends, $implements, $class_body);
    }

    function structure_namespace($namespace)
    {
        return $this->compile($this->structure['NAMESPACE'], $namespace);
    }

    function structure_logic($variable, $condition, $comparison, $if_body, $else_body)
    {
        return $this->compile($this->structure['LOGIC'], $variable, $condition, $comparison, $if_body, $else_body);
    }

    function structure_casting($data_type)
    {
        return $this->compile($this->structure['CASTING'], $data_type);
    }

    function structure_switch($data, $body)
    {
        return $this->compile($this->structure['SWITCH'], $data, $body);
    }

    function command_return($value)
    {
        return $this->compile($this->commands['RETURN'], $value);
    }

    function compile()
    {
        $argument = func_get_args();
        switch (sizeof($argument)) {
            case 2:
                return sprintf($argument[0], $argument[1]) . PHP_EOL;
                break;
            case 3:
                return sprintf($argument[0], $argument[1], $argument[2]) . PHP_EOL;
                break;
            case 4:
                return sprintf($argument[0], $argument[1], $argument[2], $argument[3]) . PHP_EOL;
                break;
            case 5:
                return sprintf($argument[0], $argument[1], $argument[2], $argument[3], $argument[4]) . PHP_EOL;
                break;
            case 6:
                return sprintf($argument[0], $argument[1], $argument[2], $argument[3], $argument[4], $argument[5]) . PHP_EOL;
                break;
        }
        return sprintf($argument[0]) . PHP_EOL;
    }

}