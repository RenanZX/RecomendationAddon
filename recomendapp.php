<?php

use Friendica\DI;
use Friendica\App;

use Friendica\Core\Hook;
use Friendica\Core\L10n;
use Friendica\Core\Logger;
use Friendica\Core\Renderer;
use Friendica\Core\Session;

use Friendica\Content\Nav;
use Friendica\Content\Text\BBCode;

use Friendica\Module\BaseProfile;

use Friendica\Protocol\DFRN;

use Friendica\Database\DBA;

use Friendica\Model\Profile;
use Friendica\Model\Contact;

use Friendica\Util\Images;
use Friendica\Util\DateTimeFormat;

use GuzzleHttp\Client;


  function recomendapp_install(){
    Hook::register('page_content_top','addon/recomend-addon/recomendapp.php','recomendapp_menu');
    Logger::notice('App instalado com sucesso!');
  }

  function recomendapp_menu($a, $b){
    DI::page()['content'] = '<b>Hello Addon</b>';
  }

  function get_recommendations($id){
    $client = new Client();
    $response = $client->request('GET','localhost:3001/generate-recomendations?id='.$id);
    $recomends = json_decode($response->getBody());
    return $recomends;
  }

?>