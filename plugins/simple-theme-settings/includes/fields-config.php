<?php 

function sts_get_fields_definition() {
  $company = [
    [
      'group' => 'company',
      'key'		=> 'name',
      'label' => 'Navn', 
      'type' 	=> 'text'
    ],
    [
      'group' => 'company',
      'key'		=> 'address',
      'label' => 'Adresse', 
      'type' 	=> 'text'
    ],
    [
      'group' => 'company',
      'key'		=> 'city',
      'label' => 'Bynavn', 
      'type' 	=> 'text'
    ],
    [
      'group' => 'company',
      'key'		=> 'postal_code',
      'label' => 'Postkode', 
      'type' 	=> 'number'
    ],
    [
      'group' => 'company',
      'key'		=> 'region',
      'label' => 'Kommune', 
      'type' 	=> 'text'
    ]
  ];

  $contact = [
    [
      'group' 			=> 'contact',
      'key'					=> 'phone',
      'label'				=> 'Telefon nummer', 
      'type'				=> 'text', 
      'description' => '(Vælg dit ønskede format, som f.eks. +45 9999 9999)'
    ],
    [
      'group' 			=> 'contact',
      'key'					=> 'email',
      'label' 			=> 'E-mail', 
      'type' 				=> 'email'
    ]
  ];

  $header = [
    [
      'group' 			=> 'header',
      'key'					=> 'logo',
      'label'				=> 'Logo',
      'type'				=>'textarea',
      'placeholder' => 'Indsæt <svg> kode her...'
    ]
  ];

  $footer = [
    [
      'group' 			=> 'footer',
      'key'					=> 'description',
      'label'				=> 'Beskrivelse',
      'type'				=> 'textarea',
      'placeholder' => 'Beskriv virksomheden...'
    ],
    [
      'group' 			=> 'footer',
      'key'					=> 'logo',
      'label'				=> 'Logo',
      'type'				=>'textarea',
      'placeholder' => 'Indsæt <svg> kode her...'
    ]
  ];

  return array_merge( $company, $contact, $header, $footer );
}

// ## the group format
// [
// 	'group' 			=> '...',
// 	'key'					=> '...',
// 	'label'				=> '...',
// 	'type'				=>'group',
// 	'fields' => [
// 		[
// 			...
// 		],
// 	],
// ],