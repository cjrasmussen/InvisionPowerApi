# InvisionPowerApi

Simple class for making requests to the Invision Power Board API.  Not affiliated with Invision Power Services.


## Usage

```php
use cjrasmussen\InvisionPowerApi\InvisionPowerApi;

$ipb = new InvisionPowerApi($token, $url);

// GET THE MEMBER DATA FOR A SPECIFIED MEMBER
$data = $ipb->request('GET', "/core/members/{$member_id}");

// RENAME A SPECIFIED FORUM
$ipb->request('PUT', "/forums/forums/{$forum_id}", ['title' => 'New Forum Title']);
```

## Installation

Simply add a dependency on cjrasmussen/invision-power-api to your composer.json file if you use [Composer](https://getcomposer.org/) to manage the dependencies of your project:

```sh
composer require cjrasmussen/invision-power-api
```

Although it's recommended to use Composer, you can actually include the file(s) any way you want.


## License

InvisionPowerApi is [MIT](http://opensource.org/licenses/MIT) licensed.