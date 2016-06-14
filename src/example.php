<?php
/**
 * Created by PhpStorm.
 * User: renato
 * Date: 17/05/16
 * Time: 16:25
 */
include_once "arm/modules/images/ImageClientSDK.php" ;
use ARM\Modules\Images;

/**
 * ATENÇÃO: É necessário o token, e existe o token com permissão de escrite e de leitura. Passe o token correspondente
 */

$config = new Images\ImageClientConfigVO();
$config->app 		= "demo" ;
//$config->url 		= "http://localhost/images_project/" ;
$config->url 		= "http://i.democrart.com.br/" ;
//$config->url 		= "http://images.idress.com.br/" ;

$config->token 		= "afdsf3kjr493_" ;

$ImageClientSDK = new Images\ImageClientSDK() ;
$ImageClientSDK->setConfig( $config ) ;

############  CRIANDO UM NOVO ALBUM ############
try {
	$resultAlbum = $ImageClientSDK->createNewAlbum("teste");
	var_dump($resultAlbum) ;
} catch ( ErrorException $e ){
	//erro ao acessar api
	var_dump( $e ) ;
}
//
die ;
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


############  EDITANDO UM ALBUM ############
//primeiro vou pegar algum album
//$resultListAlbunsWithoutTokens = $ImageClientSDK->showAlbuns() ;
//if( $resultListAlbunsWithoutTokens && count($resultListAlbunsWithoutTokens)> 0 ){
//	//claro que se não tiver album a ser editado, não edita
//	$albumVO = $resultListAlbunsWithoutTokens[0] ;
//	//alterando a ordem no album
//	$albumVO->order = rand(0, 100) ;
//	//binding ARMAlbumVO
//	$albumVO = $ImageClientSDK->bindToAlbumVO( $albumVO ) ;
//	$resultOfAlbumEdit = $ImageClientSDK->editAlbumInfo( $albumVO ) ;
//	var_dump( $resultOfAlbumEdit ) ;
//} else {
//	die("nenhum album para testar a edição") ;
//}



############  REMOVENDO UMA IMAGEM DE UM ALBUM ############
//$album_id = 1 ;
//$image_id = 1 ;
//$private_token = "";
//$resultRemoveImageAlbum = $ImageClientSDK->removeImageFromAlbum($image_id, $album_id, $private_token) ;
//var_dump( $resultRemoveImageAlbum ) ;


############  REMOVENDO UMA IMAGEM DE UM ALBUM ############
$album_id = 1 ;
$alias = "album" ;
$private_token = "";
$resultSentImage = $ImageClientSDK->sendImage("arm_logo.jpg",$album_id, $alias, $private_token ) ;
var_dump( $resultSentImage ) ;


