<?php


if ( !function_exists('partial')) {
    function partial($callable, array $arguments) {
        return new \Partial\Partial($callable, $arguments);
    }
}