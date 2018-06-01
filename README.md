# Nutshell

A PHP package for working w/ the Nutshell API.

## Install

Normal install via Composer.

## Usage

```php
use Travis\Nutshell;

$user = 'YOURUSERNAME';
$key = 'YOURAPIKEY';

// create a new contact
$contact = Nutshell::run($user, $key, 'newContact', [
	'contact' => [
		'name' => 'Johnny Quest',
		'tags' => [
			'YOURTAG',
		],
		'address' => [
			'mailing' => [
				'address_1'  => '777 Pearly Gates',
				'city'       => 'Austin',
				'state'      => 'TX',
				'postalCode' => '77777',
			],
		],
		'phone' => [
			'5555555555',
		],
		'email' => [
			'johnny@foobar.com',
		],
	],
]);

// create a note
$note = Nutshell::run($user, $key, 'newNote', [
	'entity' => [
    	'entityType' => 'Contacts',
    	'id' => $contact->id,
	],
	'note' => 'Vestibulum id ligula porta felis euismod semper.',
]);
```

See the [documentation](https://developers.nutshell.com) for more information.

## Todo

- I have in this package some code for handling file uploads, but it is incomplete.  I am waiting for the API team to allow the uploading of files for contacts.