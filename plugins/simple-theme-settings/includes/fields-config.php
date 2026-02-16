<?php 

function sts_get_fields_definition() {
  $company = [
    [
      'group'     => 'company',
      'key'		    => 'name',
      'label'     => 'Navn', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'address',
      'label'     => 'Adresse', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'city',
      'label'     => 'Bynavn', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'postal_code',
      'label'     => 'Postkode', 
      'type' 	    => 'number',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'region',
      'label'     => 'Kommune', 
      'type' 	    => 'text',
      'translate' => false
    ]
  ];

  $contact = [
    [
      'group' 			=> 'contact',
      'key'					=> 'phone',
      'label'				=> 'Telefon nummer', 
      'type'				=> 'text', 
      'description' => '(Vælg dit ønskede format, som f.eks. +45 9999 9999)',
      'translate'   => false
    ],
    [
      'group' 			=> 'contact',
      'key'					=> 'email',
      'label' 			=> 'E-mail', 
      'type' 				=> 'email',
      'translate'   => false
    ]
  ];

  $header = [
    [
      'group' 			=> 'header',
      'key'					=> 'logo',
      'label'				=> 'Logo',
      'type'				=>'textarea',
      'placeholder' => 'Indsæt <svg> kode her...',
      'translate'   => false
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
      'placeholder' => 'Indsæt <svg> kode her...',
      'translate'   => false
    ]
  ];

  $ui = [
    [
      'group'  => 'ui',
      'key'		 => 'buttons',
      'label'	 => 'Knapper',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'back',
          'label' => 'Tilbage',
          'type'  => 'text',
        ],
        [
          'key'   => 'back_to_archive',
          'label' => 'Tilbage til',
          'type'  => 'text',
        ]
      ]
    ]
  ];

  $archive = [
    [
      'group'  => 'archive',
      'key'		 => 'event',
      'label'	 => 'Event',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'heading',
          'label' => 'Overskrift',
          'type'  => 'text',
        ],
        [
          'key'   => 'description',
          'label' => 'Beskrivelse',
          'type'  => 'textarea',
        ]
      ]
    ],
    [
      'group'  => 'archive',
      'key'		 => 'post',
      'label'	 => 'Indlæg',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'heading',
          'label' => 'Overskrift',
          'type'  => 'text',
        ],
        [
          'key'   => 'description',
          'label' => 'Beskrivelse',
          'type'  => 'textarea',
        ]
      ]
    ]
  ];

  return array_merge( $company, $contact, $header, $footer, $ui, $archive );
}