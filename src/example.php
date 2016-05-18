<?php
/**
 * Created by PhpStorm.
 * User: renato
 * Date: 17/05/16
 * Time: 16:25
 */
include_once "arm/modules/images/ImageClientSDK.php" ;
use ARM\Modules\Images;

$config = new Images\ImageClientConfigVO();
$config->app 		= "teste" ;
//$config->url 		= "http://localhost/images_project/" ;
$config->url 		= "http://images.idress.com.br/" ;

$config->token 		= "" ;

$ImageClientSDK = new Images\ImageClientSDK() ;
$ImageClientSDK->setConfig( $config ) ;
############  CRIANDO UM NOVO ALBUM ############
//try {
//	$resultAlbum = $ImageClientSDK->createNewAlbum("teste");
//} catch ( ErrorException $e ){
//	//erro ao acessar api
//	var_dump( $e ) ;
//}
//var_dump($resultAlbum) ;

############  LISTANDO TODOS OS ALBUNS ############
//$resultListAllAlbuns = $ImageClientSDK->showAllAlbuns() ;
//var_dump( $resultListAllAlbuns ) ;
//
//
//############  LISTANDO ALBUNS COM O TOKEN ENVIADO ############
//$resultListAlbunsWithoutTokens = $ImageClientSDK->showAlbuns() ;
//var_dump( $resultListAlbunsWithoutTokens ) ;

////lista apenas os albuns com esse token específico
//$resultListAlbuns = $ImageClientSDK->showAlbuns("fd30efij3f03") ;
//var_dump( $resultListAlbuns ) ;

