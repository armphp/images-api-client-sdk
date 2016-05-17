<?php
/**
 * Created by PhpStorm.
 * User: renato
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
	}
	public function sendImage( $localPath, $album_id = NULL, $alias = "album" ){
		//TODO: fazer funcionar
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
	 * @return null
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
	 * @param $obj
	 * @return ARMAlbumVO
	 */
	public function bindToAlbumVO( $obj ){
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
	public function editAlbumInfo( ARMAlbumVO $albumVO){
		//TODO: fazer funcionar
	}
	public function removeImageFromAlbum( $image_id, $album_id, $private_token = "" ){
		//
	}
}
class ImageClientConfigVO{
	/**
	 * nome do seu app na api
	 * @var string
	 */
	public $app ;
	/**
	 * url de resposta da app
	 * @var string
	 */
	public $url ;
	/**
	 * Token privado da api, isso não deve ser visivel para o usuário de seu app
	 * api secret
	 * @var string
	 */
	public $token ;
}
class ARMAlbumVO {



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