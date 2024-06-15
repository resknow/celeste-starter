<?php

return [
    'class' => [ 'type' => 'string' ],
    'id' => [ 'type' => 'string' ],
    'name' => [ 'type' => 'string', 'required' => true ],
    'label' => [ 'type' => 'string', 'required' => true ],
    'type' => [ 'type' => 'string', 'default' => 'text' ],
    'showLabel' => [ 'type' => 'boolean', 'default' => true ],
];