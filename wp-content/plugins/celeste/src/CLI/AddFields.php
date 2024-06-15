<?php

namespace Celeste\CLI;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\info;

class AddFields {

    private $fields;
    private $field_types;

    public function __construct() {
        $this->fields = [];
        $this->field_types = [
            'text',
            'textarea',
            'gallery',
            'image',
            'select',
            'post_object',
            'button_group',
            'checkbox',
            'color_picker',
            'date_picker',
            'date_time_picker',
            'email',
            'file',
            'google_map',
            'link',
            'number',
            'radio',
            'time_picker',
            'true_false',
            'url',
            'wysiwyg',
        ];
    }

    public function prompts() {

        info('🧱 New Field');

        $field = [];

        $field['label'] = text(
            label: 'Label',
            placeholder: 'e.g. Field Name',
            required: true
        );

        $field['name'] = strtolower( str_replace( ' ', '_', $field['label'] ) );

        $type = search(
            label: 'Type',
            options: function( $search ) {
                return array_filter(
                    $this->field_types,
                    fn ($type) => strpos( $type, $search ) !== false
                );
            }
        );

        $field['type'] = is_int($type) ? $this->field_types[ $type ] : $type;

        // Optional fields
        $fields['required'] = confirm(
            label: 'Required?'
        );

        $field['instructions'] = text(
            label: 'Instructions',
            placeholder: 'e.g. Enter your name',
        );

        // Present field type specific options
        $field = $this->field_type_options( $field );

        $this->fields[] = $field;

        $add_another = confirm(
            label: 'Add another field?'
        );

        info( sprintf( '✅ %s field added', $field['label'] ) );

        if ( $add_another ) {
            $this->prompts();
        }

       return $this->fields;

    }

    /**
     * Field Type Options
     */
    public function field_type_options( array $field ) {

        switch ( $field['type'] ) {
            case 'number':
            case 'gallery':
                $field['min'] = text(
                    label: 'Minimum Value'
                );
                $field['max'] = text(
                    label: 'Maximum Value'
                );
                break;

            case 'post_object':
                $field['post_type'] = search(
                    label: 'Post Type',
                    options: function( $search ) {
                        return array_filter(
                            get_post_types(),
                            fn ($type) => strpos( $type, $search ) !== false
                        );
                    },
                );

                $field['multiple'] = confirm(
                    label: 'Allow multiple selections?',
                );

                $field['allow_null'] = confirm(
                    label: 'Allow empty selection?',
                    default: false
                );

                $field['return_format'] = select(
                    label: 'Return Format',
                    options: [
                        'object' => 'Object',
                        'id' => 'ID'
                    ]
                );
                break;

            case 'wysiwyg':
                $field['tabs'] = select(
                    label: 'Tabs',
                    options: [
                        'all' => 'All',
                        'visual' => 'Visual Only',
                        'text' => 'Text Only'
                    ],
                    default: 'visual'
                );
                $field['toolbar'] = select(
                    label: 'Tabs',
                    options: [
                        'full' => 'Full',
                        'basic' => 'Basic'
                    ],
                    default: 'basic'
                );
                $field['media_upload'] = confirm(
                    label: 'Allow Media Upload?',
                    default: false
                );
                break;

            case 'file':
                $mime_types = text(
                    label: 'Allowed MIME Types',
                    hint: 'Comma seperated list. See bit.ly/3QLFKmX for common MIME types.'
                );

                $field['mime_types'] = $mime_types ? explode(',', $mime_types) : null;
                break;

            case 'select':
                $field['multiple'] = confirm(
                    label: 'Allow multiple selections?',
                );
                $field['ui'] = confirm(
                    label: 'Use Custom UI?'
                );
                $field['ajax'] = confirm(
                    label: 'Lazy Load Options?',
                    hint: 'Can improve performance if you have a large list of choices'
                );
                $field = $this->add_choice($field);
                break;
        }

        return $field;

    }

    public function add_choice( array $field ) {

        info('✅ Add a Choice');

        $value = text(
            label: 'Value',
            required: true
        );

        $label = text(
            label: 'Label',
            required: true
        );

        $field['choices'][$value] = $label;

        $add_another = confirm(
            label: 'Add another choice?',
        );

        if ( $add_another ) {
            $field = $this->add_choice($field);
        }

        return $field;
    }

}