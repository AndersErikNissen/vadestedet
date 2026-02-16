<?php
/**
 * Plugin Name: Advanced Custom Fields - Group Generator (ACFGG)
 * Description: This plugin is creating field groups for the plugin ACF (Advanced Custom Fields), so you don't have to create them manually. You are free to add your own, and if you don't want these premade ones, just disable/uninstall this plugin.
 * Version: 1.0
 * Author: AENDERS.DK
 * Author URI: https://aenders.dk
 */


// @@ HIDE ACF FROM BACKEND
// add_filter( 'acf/settings/show_admin', '__return_false' );


// @@ ADD CSS TO WYSIWYG
add_action( 'after_setup_theme', function() {
  add_editor_style( plugin_dir_url( __FILE__ ) . 'css/acfgg-editor.css' );
});


// @@ CLEAN UP WYSIWYG TOOLBAR
add_filter( 'acf/fields/wysiwyg/toolbars', function ( $toolbars ) {
  // ## remove full (the standard toolbar) entirely
  unset( $toolbars['Full'] );

  // ## replace with your own
  $toolbars['Full'] = [];
  $toolbars['Full'][1] = [
    'formatselect',
    'bold',
    'italic',
    'strikethrough',
    'link',
    'bullist',
    'numlist',
    'blockquote',
    'alignleft',
    'aligncenter',
    'alignright',
    'undo',
    'redo'
  ];

  return $toolbars;
}, 1 );


// @@ EDIT THE TINYMCE BLOCK OPTIONS
add_filter( 'tiny_mce_before_init', function( $init_array ) {
  $init_array['block_formats'] = 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4';
  return $init_array;
});


// @@ HIDE CURRENT POST FROM RELATIONSHIP FIELDS
add_filter('acf/fields/relationship/query', function( $args, $field, $post_id ) {
  // ## adds the current post's ID to the 'post__not_in' array, to exclude it from the query
  $args[ 'post__not_in' ] = array( $post_id );
  return $args;
}, 10, 3);


// @@ SYNC IMAGE FIELD TO FEATURED IMAGE
function sync_acf_list_to_featured_image( $post_id, $field_names ) {
  $new_thumb_id = null;

  // ## look for the first matching field in the list
  foreach ( $field_names as $field_name ) {
    $value = get_field($field_name, $post_id);
    
    if ( $value ) {
      $new_thumb_id = is_array( $value ) ? $value[ 'ID' ] : $value;
      break;
    }
  }

  $current_thumb_id = get_post_thumbnail_id( $post_id );

  if ( $new_thumb_id !== $current_thumb_id ) {
    if ( $new_thumb_id ) {
      update_post_meta( $post_id, '_thumbnail_id', $new_thumb_id );
    } else {
      delete_post_meta( $post_id, '_thumbnail_id' );
    };
  };
}

add_action('acf/save_post', function( $post_id ) {
  // ## bail if this is an autosave or a revision
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  if ( wp_is_post_revision( $post_id ) ) return;

  // ## first matching field wins
  $fields_to_check = [
    'section_event_information_post_description_block_image'
  ];

  sync_acf_list_to_featured_image( $post_id, $fields_to_check );
}, 20);


// @@ GENERATE UNIQUE KEY
function acfgg_key( $relation, $name, $context = 'field_' ):string {
  return $context . substr( md5( $relation . '_' . $name ), 0, 12);
}


// @@ GENERATE FIELD
function acfgg_field( $relation, $label, $name, $type, $args = [] ):array {
  return array_merge( [
    'key'   => acfgg_key( $relation, $name ),
    'label' => $label,
    'name'  => $relation . $name,
    'type'  => $type,
  ], $args );
}


// @@ GENERATE ACCORDION
function acfgg_accordion( $relation, $name, $instructions = '', $open = 0 ):array {
  return [
    'key'   => acfgg_key( $relation, $name ),
    'label' => $name,
    'type'  => 'accordion',
    'open'  => $open,
    'instructions' => $instructions,
  ];
}


// @@ GENERATE BLOCK
function acfgg_block( $relation, $type, $count = '' ):array {
  $block_relation = $relation . $type . '_block' . $count . '_';

  if ( $type === 'text' ) {
    return [
      acfgg_accordion( $block_relation . 'tab_', 'Tekst indhold'                     ),
      acfgg_field(     $block_relation,          'Overskrift', 'heading', 'text'     ),
      acfgg_field(     $block_relation,          'Tekst',      'text',    'textarea' ),
      acfgg_field(     $block_relation,          'Knap',       'button',  'link'     )
    ];
  };

  if ( $type === 'image' ) {
    $image_ratio_args = [
      'choices' => [
        'default'     => 'Original',
        '4:5'         => 'Portræt',
        '1:1'         => 'Kvadratisk',
        '16:9'        => 'Landskab',
        '4:1.5'       => 'Banner'
      ],
      'default_value' => 'default'
    ];

    return [
      acfgg_accordion( $block_relation . 'tab_', 'Billede opsætning'                                                                      ),
      acfgg_field(     $block_relation,          'Vælg billede (Computer)',      'image_desktop',       'image', [ 
        'wrapper' => [
          'width' => '50'
        ] 
      ]                                                                                                                                   ),
      acfgg_field(     $block_relation,          'Vælg billede (Mobile)',        'image_mobile',        'image', [ 
        'wrapper' => [
          'width' => '50'
        ] 
      ]                                                                                                                                   ),
      acfgg_field(     $block_relation,          'Vælg billedformat (Computer)', 'image_ratio_desktop', 'button_group', $image_ratio_args ),
      acfgg_field(     $block_relation,          'Vælg billedformat (Mobil)',    'image_ratio_mobile',  'button_group', $image_ratio_args ),  
      acfgg_field(     $block_relation,          'Vis billede først på',         'image_first',         'button_group', [
        'choices' => [
          'default'        => 'Ingen',
          'desktop'        => 'Computer',
          'mobile'         => 'Mobil',
          'desktop/mobile' => 'Computer & Mobil',
        ],
        'default_value'    => 'default'                                                                                                     
      ]                                                                                                                                   )
    ];
  };

  if ( $type === 'post_description' ) {
    return [
      acfgg_accordion( $block_relation . 'tab_', 'Beskrivelse'                                         ),
      acfgg_field(     $block_relation,          'Overskrift',       'heading',           'text', [
        'required' => true
      ]                                                                                                ),
      acfgg_field(     $block_relation,          'Kort beskrivelse', 'short_description', 'textarea', [
        'required'     => true
      ]                                                                                                ),
      acfgg_field(     $block_relation,          'Billede',          'image',             'image'      ),
      acfgg_field(     $block_relation,          'Beskrivelse',      'description',       'wysiwyg', [
        'tabs'         => 'visual',
        'media_upload' => 1,
        'delay'        => 0,
        'required'     => true                                                             
      ]                                                                                                ),
      acfgg_field(     $block_relation,          'Knap',             'button',            'link'       )
    ];
  };

  if ( $type === 'event_information' ) {
    return [
      acfgg_accordion( $block_relation . 'tab_', 'Event information'                                   ),
      acfgg_field(     $block_relation,          'Event navn',          'event_name', 'text', [
        'required' => true
      ]                                                                                                ),
      acfgg_field(     $block_relation,          'Event dato',          'date',       'date_picker', [
        'display_format' => 'd/m/Y',
        'return_format'  => 'Ymd',
        'required' => true
      ]                                                                                                ),
      acfgg_field(     $block_relation,          'Sæt start tidspunkt', 'start_time', 'time_picker', [
        'display_format' => 'H:i:s',
        'return_format'  => 'H:i:s',
        'required'       => true
      ]                                                                                                ),
      acfgg_field(     $block_relation,          'Sæt slut tidspunkt',  'end_time',   'time_picker', [
        'display_format' => 'H:i:s',
        'return_format'  => 'H:i:s',
        'required'       => true
      ]                                                                                                )
    ];
  };

  if ( $type === 'event_relationship' ) {
    return [
      acfgg_accordion( $block_relation . 'tab_', 'Relation(er)',             'Relevant hvis dette event har en relation til 1 eller flere events'                  ),
      acfgg_field(     $block_relation,          'Vælg relaterede event(s)', 'event_relationship',                                                'relationship', [
        'post_type' => 'event',
        'filters'   => '',
        'elements'  => [ 'featured_image' ]
      ]                                                                                                                                                            )
    ];
  }
}

// @@ GENERATE LOCATION
function acfgg_location( $locations ):array {
  $selected_locations = [];

  $awailable_locations = [
    'frontpage' => [
      "param"    => "page_type",
      "operator" => "==",
      "value"    => "front_page"
    ],
    'post' => [
      'param'    => 'post_type',
      'operator' => '==',
      'value'    => 'post',
    ],
    'page' => [
      'param'    => 'post_type',
      'operator' => '==',
      'value'    => 'page',
    ],
    'event' => [
      'param'    => 'post_type',
      'operator' => '==',
      'value'    => 'event',
    ],
  ];

  foreach( $locations as $location ) {
    $available_location = ( $awailable_locations[ $location ] ?: false );

    if ( $available_location ) {
      array_push( $selected_locations, $available_location );
    }
  }

  return $selected_locations;
}


// @@ GENERATE GROUP
function acfgg_group( $relation, $name, $section, $fields, $location, $menu_order = 9 ) {
  acf_add_local_field_group( [
    'key'        => acfgg_key( $relation, $name, '_group_' ),
    'title'      => $name,
    'acfgg' => [ // Custom key, used only by this plugin
      'relation' => $relation,
      'section'  => $section,
    ], 
    'fields'     => $fields,
    'location'   => $location,
    'menu_order' => $menu_order,
  ] );
}


// @@ CREATE ALL SECTIONS
function acfgg_sections() {
  // ## post
  $relation = 'section_post_information_';
  
  acfgg_group( 
    $relation, 
    'Section: Event information', 
    'event-information',
    array_merge(
      acfgg_block( $relation, 'post_description' ), 
    ), [
      acfgg_location( [ 'post' ] )
    ]
  );


  // ## event (cpt)
  $relation = 'section_event_information_';
  
  acfgg_group( 
    $relation, 
    'Section: Event information', 
    'event-information',
    array_merge(
      acfgg_block( $relation, 'post_description'   ), 
      acfgg_block( $relation, 'event_information'  ),
      acfgg_block( $relation, 'event_relationship' )
    ), [
      acfgg_location( [ 'event' ] )
    ]
  );


  // ## text and image
  $relation = 'section_text_and_image_';

  acfgg_group( 
    $relation, 
    'Sektion: Tekst & billede', 
    'text-and-image',
    array_merge(
      acfgg_block( $relation, 'text'  ),
      acfgg_block( $relation, 'image' )
    ), [
      acfgg_location( [ 'post' ] ),
      acfgg_location( [ 'page' ] )
    ]
  );
}

add_action('acf/init', 'acfgg_sections');