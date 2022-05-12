
  
<?php
/**
 * Name: Recommend Addon
 * Description: Aplicativo de recomendacoes por comentarios
 * Version: 1.0
 * Author: RZ
 */

require_once('class/coments.php');
require_once('class/recomendations.php');

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
    Hook::register('profile_tabs', 'addon/recomendapp/recomendapp.php', 'recomendapp_profile_tabs');
    //system('go run servidor/server.go')
    Disciplina\install_dps();
    Disciplina\install_categories();
  }

  function recomedapp_uninstall(){
    Hook::unregister('profile_tabs', 'addon/recomendapp/recomendapp.php', 'recomendapp_profile_tabs');
    //system('killall servidor/server.go')
    Disciplina\uninstall_dps();
    Disciplina\uninstall_categories();
  }

  function recomendapp_profile_tabs($a, &$b){
    $recommend_tb = [
      'label' => 'Recomendacoes',
      'url' => 'recomendapp/profile/' . $b['nickname'] .'/recomendacoes',
      'title' => 'Recomendacoes',
      'id' => 'recomendacoes-tab',
      'accesskey' => 'rt',
    ];
    $comment_tb = [
      'label' => 'Comentarios',
      'url' => 'recomendapp/profile/' . $b['nickname'].'/comentarios',
      'title' => 'Comentarios',
      'id' => 'comentarios-tab',
      'accesskey' => 'ct',
    ];
    $db_tb = [
      'label' => 'Disciplinas',
      'url' => 'recomendapp/disciplinas',
      'title' => 'Disciplinas',
      'id' => 'disciplinas-tab',
      'accesskey' => 'dp',
    ];
  
    $b['tabs'][] = array_splice($b['tabs'], 6, 0, [$comment_tb]);
    array_push($b['tabs'], $recommend_tb, $db_tb);
  }

  function recomendapp_module() {
  }

  function recomendapp_init(App $a){
    Nav::setSelected('home');
  
    if (DI::args()->getArgc() >= 2) {
		  if(DI::args()->getArgv()[1] == "profile"){
        $id = getIdUserProfile($a);
        
        $profile = Profile::getByUID($id);

        Badge\compute_reputation_user($id);
        Disciplina\enter_in_buble($id);
        
        $tpl = Renderer::getMarkupTemplate("profile/vcard.tpl");
    
        $vcard_widget = Renderer::replaceMacros($tpl, [
          '$profile' => $profile,
        ]);
        
        if (empty(DI::page()['aside'])) {
          DI::page()['aside'] = '';
        }
        DI::page()['aside'] .= $vcard_widget;
      }else if(DI::args()->getArgv()[1] == "disciplinas" && DI::args()->getArgc() < 3){
        $tpl = Renderer::getMarkupTemplate("disciplinas.tpl", "addon/recomendapp/"); //pega o template de comentarios

        $dps = Disciplina\get_list();

        $content = Renderer::replaceMacros($tpl, [
          '$dps' => $dps
        ]);
        DI::page()['content'] = $content;
      }
    }
  }

  function recomendapp_post(App $a){
    if($_POST["type"] == '1'){ //Likes deslikes e deletar comentario
      if(array_key_exists('delete', $_POST)){
        Comentarios\delete_coment($_POST['id_coment']);
      }else if(array_key_exists('like_x', $_POST)){
        Comentarios\update_like($_POST['id_coment'],$_POST['id']);
      }else if(array_key_exists('deslike_x', $_POST)){
        Comentarios\update_deslike($_POST['id_coment'],$_POST['id']);
      }
    } else if($_POST["type"] == '2'){ //Comentários do Perfil
      $id_destino = getIdUserProfile($a);
      if(strlen($_POST['coment-text']) > 5){
        Comentarios\post_coment($_POST['id_user'], $id_destino, $_POST['coment-text'], $_POST['disciplina']);
      }
    } else if($_POST['type'] == '3'){ //Comentários das disciplinas
      $x = intval($_POST['star_x']);
      $star = 0;
      if($x > 0 && $x <= 53){
        $star = 1;
      }else if($x > 65 && $x <= 114){
        $star = 2;
      }else if($x > 127 && $x <= 172){
        $star = 3;
      }else if($x > 188 && $x <= 232){
        $star = 4;
      }else if($x > 249){
        $star = 5;
      }
      if(strlen($_POST['coment-text']) > 5){
        Comentarios\post_coment_dp($_POST['id_user'], $_POST['id_dp'],$star, $_POST['coment-text']);
      }
    } else if($_POST['type'] == '4'){ //Feedback do Recomendador
      $x = intval($_POST['feed_x']);
      if($x >= 0 && $x < 54){ //feliz
        $smile = 1;
      }else if($x >= 71 && $x < 125){ //indiferente
        $smile = 2;
      }else if($x >= 144){ //insatisfeito
        $smile = 3;
      }
      Recomendador\insert_feedback($_POST['id_user'], $smile);
    } else if($_POST['type'] == '5'){ //Inscrição na disciplina recomendada
      if($_POST['acc'] == 'SIM'){
        //redireciona para a página de matrícula
      }
    }
  }

  function getIdUserProfile(App $a){
    $nick = DI::args()->getArgv()[2];
    try{
      $q = DBA::select('user', array('uid'), array('nickname'=>$nick));
      if($r = DBA::fetch($q)){
        return $r['uid'];
      }
    }catch(Exception $e){
      Logger::debug($e->getMessageError());
    }
    return -1;
  }

  function recomendapp_content(App $a){
    
    $user = '';
    if($a->isLoggedIn()){ //Testa se o usuario esta ativo
      $user = $a->getLoggedInUserNickname();
      $id = $a->getLoggedInUserId();
    }
    $profile_user = Profile::getByUID($id); //pega o perfil do usuario
    if(DI::args()->getArgc() > 3 && DI::args()->getArgv()[1] != 'disciplinas'){
      $profile_id = getIdUserProfile($a); //Pega o profile da pagina do usuario
      $profile_page = Profile::getByUID($profile_id); //pega o perfil da pagina
    }

    $content = ''; //conteudo a ser retornado ao usuario

    if(DI::args()->getArgc() > 3 && DI::args()->getArgv()[1] == 'disciplinas' && DI::args()->getArgv()[2] == 'id'){
      $tpl = Renderer::getMarkupTemplate("disciplina_page.tpl", "addon/recomendapp/"); //pega o template de comentarios
      $dp_id = DI::args()->getArgv()[3];

      $dp = Disciplina\get_disciplina_by_id($dp_id);
      $perfil_coments = Comentarios\get_comentarios_dp($dp_id);

      $dp_comentada = Comentarios\is_dp_comentada($id, $dp_id);

      $content .= Renderer::replaceMacros($tpl, [
        '$title' => $dp['Nome'],
        '$description' => $dp['Descricao'],
        '$profile' => $profile_user,
        '$star_comment' => DI::baseUrl()->get().'/addon/recomendapp/assets/rating.png',
        '$coments' => $perfil_coments,
        '$user_id' => $id,
        '$dp_id' => $dp_id,
        '$dp_comentada' => $dp_comentada
      ]);
    }else if(DI::args()->getArgc() > 3 && DI::args()->getArgv()[3] == 'comentarios') {
      $tpl = Renderer::getMarkupTemplate("comentarios.tpl", "addon/recomendapp/"); //pega o template de comentarios

      //Logger::debug('profile: '.json_encode($profile));
      $perfil_coments = Comentarios\get_comentarios($profile_id);
      $show_form = ($id != $profile_id);

      //Computar nivel do usuario e inserir a badge
      $dps_user = Comentarios\compute_association_coment($profile_id);
      //$dps_user = ['Fisica','Quimica','Outros'];
      $badge_level = Badge\get_reputation_user($profile_id);
      $len = count($dps_user);
      $others = array_slice($dps_user, 2, $len);
      $first_two = array_slice($dps_user, 0, 2);

      $content .= BaseProfile::getTabsHTML($a, 'comentarios', true,'profile', $user);
      $content .= Renderer::replaceMacros($tpl, [
	      '$title' => 'Comentarios',
        '$profile' => $profile_user,
        '$profile_name' => $profile_page['name'],
        '$like'=> DI::baseUrl()->get().'/addon/recomendapp/assets/like.png',
        '$deslike' => DI::baseUrl()->get().'/addon/recomendapp/assets/deslike.png',
        '$level' => DI::baseUrl()->get().'/addon/recomendapp/assets/'.$badge_level.'.png',
        '$user_id' => $id,
        '$show_form' => $show_form,
        '$coments' => $perfil_coments,
        '$dps_user' => $first_two,
        '$all_dps' => $others
	    ]);
    }else if(DI::args()->getArgc() > 3 && DI::args()->getArgv()[3] == 'recomendacoes'){
      $tpl = Renderer::getMarkupTemplate("recomendados.tpl", "addon/recomendapp/"); //pega o template de comentarios

      $table = Recomendador\get_recommendations($id);
      //$table = ['Recomendados'=>[],'NRecomendados'=>[]];
      $badge_level = Badge\get_reputation_user($id);
      $show_form = ($id != $profile_id);

      $content .= BaseProfile::getTabsHTML($a, 'recomendacoes', true,'profile', $user);
      $content .= Renderer::replaceMacros($tpl, [
        '$profile' => $profile_user,
        '$show_form' => $show_form,
        '$comunidade' => $table['comunidade'],
        '$Recomendados' => $table['Recomendados'],
        '$NRecomendados' => $table['NRecomendados'],
        '$user_id' => $id,
        '$feedb' => DI::baseUrl()->get().'/addon/recomendapp/assets/options.png',
        '$level' => DI::baseUrl()->get().'/addon/recomendapp/assets/'.$badge_level.'.png',
		  ]);
    }

    //Logger::debug($content);

    return $content;
  }

?>