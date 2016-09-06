<?php

/**
 * Created by PhpStorm.
 * User: Renato Miawaki
 * Date: 17/05/16
 * Time: 15:13
 */
namespace ARM\Modules\Images;
class ImageClientSDK {
	/**
	 * @var ImageClientConfigVO
	 */
	protected $_config ;
	public function setConfig( ImageClientConfigVO $config ){
		$this->_config = $config ;
		//precisa da barra
		$this->_config->url = \ARMDataHandler::removeLastBar( $this->_config->url )."/";
	}

	/**
	 * Troca o token interno
	 * @param $token
	 */
	public function changeToken( $token ){
		$this->_config->token = $token ;
	}
	/**
	 * Retorna o src da imagem original
	 * Útil apenas para projetos em que o módulo sdk está instalado no mesmo servidor.
	 *
	 * @param $id
	 * @param null $alias
	 * @return something|null
	 * @throws \ErrorException
	 */
	public function getImageRawSrc( $id , $alias = NULL ){
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token,
			"alias"=>$alias,
			"raw"=>1
		) ) ;
		$url = $this->_config->url ."image/show/id.$id/?".$query;
		$resultString = file_get_contents( $url ) ;
		$result = json_decode( $resultString , FALSE, 512,  JSON_UNESCAPED_SLASHES ) ;
		return $this->getResultHandled( $result ) ;
	}
	/**
	 * Retorna a url da imagem no projeto configurado
	 * @param $id
	 * @param null $width
	 * @param null $height
	 * @param null $mode - ver os modos possíveis de crop e proporções
	 * @param int $quality
	 * @return string
	 */
	public function getImageUrl( $id, $width = NULL, $height = NULL, $mode = NULL , $quality = 100 ){
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token,
			"width"=>$width,
			"height"=>$height,
			"mode"=>$mode,
			"quality"=>$quality
		) ) ;
		$url = $this->_config->url ."image/show/id.$id/?".$query;
		return $url ;

	}
	/**
	 * Envia um arquivo local para um album
	 * @param $localPath file
	 * @param null $album_id
	 * @param string $alias
	 * @param string $private_token
	 * @return mixed
	 * @throws \ErrorException
	 */
	public function sendImage( $localPath, $album_id = NULL, $alias = "album" , $private_token = "" ){
		if(!file_exists($localPath)){
			throw new \ErrorException("file not exists $localPath ") ;
		}
		$query = http_build_query( array(
			"private_token"=>$private_token,
			"app"=> $this->_config->app,
			"alias"=> $alias,
			"album_id"=> $album_id,
			"var_name"=> "file"
		) ) ;
		$url = $this->_config->url ."image/save/?".$query;
		$ch = curl_init( $url ) ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
		curl_setopt($ch, CURLOPT_POST, 1) ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
		$mime = mime_content_type( $localPath ) ;
		$args["token"] = $this->_config->token ;
		$args['file'] = new \CurlFile( $localPath , $mime , basename( $localPath ) );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args) ;
		return curl_exec($ch);
	}
	/**
	 * Cria um novo album e retorna as infos salvas
	 * @param $name
	 * @param string $description
	 * @param string $private_token
	 * @return ARMAlbumVO|null
	 */
	public function createNewAlbum( $name, $description = "", $private_token = "" ){
		$query = http_build_query( array(
			"name"=>$name,
			"description"=>$description,
			"private_token"=>$private_token,
			"app"=> $this->_config->app
		,
			"token"=> $this->_config->token,
		) ) ;
		$url = $this->_config->url ."album/create_new/?".$query;
		$resultString = file_get_contents( $url ) ;
		$result = json_decode( $resultString, FALSE, 512, JSON_UNESCAPED_SLASHES ) ;
		$returnResult = $this->getResultHandled( $result ) ;
		if( $returnResult && is_object( $returnResult ) && isset( $returnResult->success ) && $returnResult->result ){
			$obj = $returnResult->result ;
			//bind
			if( isset( $obj->id ) ){
				return $this->bindToAlbumVO( $obj ) ;
			}
		}
	}
	/**
	 * Retorna um erro tratado e já gera excessão caso não funcione
	 * @param $result
	 * @return null|something
	 * @throws \ErrorException
	 */
	protected function getResultHandled( $result ){
		if($result->code != 200){
			throw new \ErrorException( $result->result ) ;
		}
		if( $result->result ) {
			return $result->result ;
		}
		return NULL ;
	}
	/**
	 * Bind entre obj enviado e ARMAlbumVO
	 * @param $obj
	 * @return ARMAlbumVO|null
	 */
	public function bindToAlbumVO( $obj ){
		if(!$obj){
			return NULL;
		}
		$albumVO = new ARMAlbumVO() ;
		$albumVO->id = (isset($obj->id))?$obj->id:NULL ;
		$albumVO->active = (isset($obj->active))?$obj->active:NULL ;
		$albumVO->description = (isset($obj->description))?$obj->description:NULL ;
		$albumVO->name = (isset($obj->name))?$obj->name:NULL ;
		$albumVO->order = (isset($obj->order))?$obj->order:NULL ;
		$albumVO->private_token = (isset($obj->private_token))?$obj->private_token:NULL ;
		return $albumVO ;
	}
	/**
	 * lista as imagens de um album
	 * @param $album_id
	 * @param null $alias
	 * @param null $private_token
	 * @return something|null
	 * @throws \ErrorException
	 */
	public function showImagesOfAlbum( $album_id, $alias = NULL, $private_token = NULL ){
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token,
			"private_token"=>$private_token,
			"alias"=>$alias,
			"album_id"=>$album_id
		) ) ;
		$url = $this->_config->url ."album/show/?".$query;
		$resultString = file_get_contents( $url ) ;
		$result = json_decode( $resultString , FALSE, 512,  JSON_UNESCAPED_SLASHES ) ;
		return $this->getResultHandled( $result ) ;
	}
	/**
	 * @param string $private_token
	 * @return ARMAlbumVO[]
	 */
	public function showAlbuns( $private_token = "" ){
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token,
			"private_token"=>$private_token
		) ) ;
		$url = $this->_config->url ."album/show/?".$query;
		$resultString = file_get_contents( $url ) ;
		$result = json_decode( $resultString , FALSE, 512,  JSON_UNESCAPED_SLASHES ) ;
		return $this->getResultHandled( $result ) ;
	}
	/**
	 * @param string $private_token
	 * @return ARMAlbumVO[]
	 */
	public function showAllAlbuns(){
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token,
			"show_all"=>1
		) ) ;
		$url = $this->_config->url ."album/show/?".$query;
		$resultString = file_get_contents( $url ) ;
		$result = json_decode( $resultString , FALSE, 512,  JSON_UNESCAPED_SLASHES ) ;
		return $this->getResultHandled( $result ) ;
	}
	/**
	 *
	 * retorno { success:bool, result:ARMAlbumVO, array_messages:string[] }
	 * @param ARMAlbumVO $albumVO
	 * @return ARMReturnResultVO|null
	 * @throws \ErrorException
	 */
	public function editAlbumInfo( ARMAlbumVO $albumVO){
		if(!$albumVO->id){
			throw new \ErrorException("album id?") ;
		}
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token
		) ) ;
		$postdata = http_build_query(
			(array) $albumVO
		);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);
		$url = $this->_config->url ."album/edit/?".$query;
		$context  = stream_context_create($opts);
		$resultString = file_get_contents($url, false, $context) ;
		$result = json_decode( $resultString , FALSE, 512,  JSON_UNESCAPED_SLASHES ) ;
		return $this->getResultHandled( $result ) ;
	}
	/**
	 * Desvincula uma imagem a um album
	 * @param $image_id
	 * @param $album_id
	 * @param string $private_token
	 * @return bool|null
	 * @throws \ErrorException
	 */
	public function removeImageFromAlbum( $image_id, $album_id, $private_token = "" ){
		if(!$image_id || !$album_id){
			return NULL;
		}
		$query = http_build_query( array(
			"app"=> $this->_config->app ,
			"token"=> $this->_config->token,
			"image_id" =>$image_id,
			"album_id" =>$album_id,
			"private_token"=>$private_token
		) ) ;
		$url = $this->_config->url ."album/remove_image_from_album/?".$query;
		//
		$resultString = file_get_contents( $url ) ;
		$result = json_decode( $resultString , FALSE, 512,  JSON_UNESCAPED_SLASHES ) ;
		return $this->getResultHandled( $result ) ;
	}
}
if( ! class_exists( "ImageClientConfigVO" ) ) {
	class ImageClientConfigVO
	{
		/**
		 * nome do seu app na api
		 * @var string
		 */
		public $app;
		/**
		 * url de resposta da app
		 * @var string
		 */
		public $url;
		/**
		 * Token privado da api, isso não deve ser visivel para o usuário de seu app
		 * api secret
		 * @var string
		 */
		public $token;
	}
}
if( ! class_exists( "ARMAlbumVO" ) ) {
	class ARMAlbumVO
	{
		/**
		 * @type : int(11)
		 */
		public $id;
		/**
		 * @type : int(11)
		 */
		public $active;
		/**
		 * @type : int(11)
		 */
		public $order;
		/**
		 * @type : varchar(255)
		 */
		public $name;
		/**
		 * @type : text
		 */
		public $description;
		/**
		 * @type : varchar(255)
		 */
		public $private_token;
	}
}