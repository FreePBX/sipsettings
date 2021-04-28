<?php

namespace FreePBX\modules\SipSettings\Api\Gql;

use GraphQLRelay\Relay;
use GraphQL\Type\Definition\Type;
use FreePBX\modules\Api\Gql\Base;

class SipSettings extends Base {
	protected $module = 'Sipsettings';
	
	/**
	 * queryCallback
	 *
	 * @return void
	 */
	public function queryCallback() {
		if($this->checkAllReadScope()) {
			return function() {
				return [
					'fetchSipNatNetworkSettings' => [
						'type' => $this->typeContainer->get('sipsettings')->getConnectionType(),
						'resolve' =>  function() {
							return $this->getNetworkSettings();
						}
					],
            ];
			};
	   }
	}
	
	public function mutationCallback() {
		if($this->checkAllWriteScope()) {
			return function() {
				return [
					'addSipNatLocalIp' => Relay::mutationWithClientMutationId([
						'name' => _('addSipNatLocalIp'),
						'description' => _('Adding a Local IP network and mask'),
						'inputFields' => $this->getInputFields(),
						'outputFields' => $this->getOutputFields(),
						'mutateAndGetPayload' => function($input){
							return $this->addLocalIP($input);
						}
					]),
					'updateSipNatExternalIp' => Relay::mutationWithClientMutationId([
						'name' => _('updateSipNatExternalIp'),
						'description' => _('Updating External IP network and mask'),
						'inputFields' => $this->getUpdateField(),
						'outputFields' => $this->getOutputFields(),
						'mutateAndGetPayload' => function($input){
							return $this->updateExternalIP($input);
						}
					])
				];
			};
		}
	}
	
	/**
	 * getInputFields
	 *
	 * @return void
	 */
	private function getInputFields(){
		return [
		 	'net' => [
				'type' => Type::nonNull(Type::string()),
				'description' => _('The network IP address')
			],
		 	'mask' => [
				'type' => Type::nonNull(Type::string()),
				'description' => _('The network mask')
			]
		];
	}
		
	/**
	 * getUpdateField
	 *
	 * @return void
	 */
	private function getUpdateField(){
		return [
		 	'net' => [
				'type' => Type::nonNull(Type::string()),
				'description' => _('ertyuiop')
			]
		];
	}
	
	/**
	 * getOutputFields
	 *
	 * @return void
	 */
	private function getOutputFields(){
		return [
			'status' => [
				'type' => Type::boolean(),
				'description' => _('API status')
			],	
			'message' => [
				'type' => Type::string(),
				'description' => _('API message response')
			]		
		];
	}

	/**
	 * initializeTypes
	 *
	 * @return void
	 */
	public function initializeTypes() {
		$sipsettings = $this->typeContainer->create('sipsettings');
		$sipsettings->setDescription(_('Sipsettings management'));

		$sipsettings->addInterfaceCallback(function() {
			return [$this->getNodeDefinition()['nodeInterface']];
		});

	   $sipsettings->addFieldCallback(function() {
		 return [
			'id' => Relay::globalIdField('sipsettings', function($row) {
				return isset($row['id']) ? $row['id'] : null;
			}),
			'status' =>[
				'type' => Type::boolean(),
				'description' => _('Status of the request')
			],
			'message' =>[
				'type' => Type::String(),
				'description' => _('Message for the request')
			],
			'net' =>[
				'type' => Type::String(),
				'description' => _('Returns the network IP')
			],
			'mask' =>[
				'type' => Type::String(),
				'description' => _('Returns the network mask')
			]
		];
	});

	$sipsettings->setConnectionFields(function() {
		return [
			'localIP' => [
				'type' =>  Type::listOf($this->typeContainer->get('sipsettings')->getObject()),
				'description' => _('list of local IP saved'),
				'resolve' => function($root, $args) {
					$data = array_map(function($row){
						return $row;
					},isset($root['localIP']) ? $root['localIP'] : []);
						return $data;
					}
				],
			'routes' => [
				'type' =>  Type::listOf($this->typeContainer->get('sipsettings')->getObject()),
				'description' => _('list the route configured'),
				'resolve' => function($root, $args) {
					$data = array_map(function($row){
						return $row;
					},isset($root['routes']) ? $root['routes'] : []);
						return $data;
					}
				],
			'message' =>[
				'type' => Type::string(),
				'description' => _('Message for the request')
				],
			'status' =>[
				'type' => Type::boolean(),
				'description' => _('Status for the request')
				],
			'externIP' =>[
				'type' => Type::String(),
				'description' => _('Lists the External IPs')
				]
		   ];
	   });
   }
	
	/**
	 * getNetworkSettings
	 *
	 * @return void
	 */
	public function getNetworkSettings(){
		try {
			$ip = $this->freepbx->sipsettings->getNatObj()->getVisibleIP();
			$routeArr = $this->freepbx->sipsettings->getNatObj()->getRoutes();
			$routes = array();
			foreach($routeArr as $res){
				array_push($routes,array('net' => $res[0],'mask' => $res[1]));
			}
			if($ip['status']) {
				$retarr = array("message" => _("List of External and Local IPs"), "status" => true, "externIP" => $ip['address'], "routes" => $routes,'localIP' => $this->freepbx->sipsettings->getNatObj()->getConfigurations('localnets',$this->freepbx));
			} else {
				$retarr = array("message" => $ip['message'], "status" => true, "externIP" => false, "routes" => $routes , 'localIP' => $this->freepbx->sipsettings->getNatObj()->getConfigurations('localnets',$this->freepbx));
			}
		} catch(\Exception $e) {
			$retarr = array("status" => false, "message" => $e->getMessage());
		}
		return $retarr;
	}
	
	/**
	 * addLocalIP
	 *
	 * @param  mixed $input
	 * @return void
	 */
	private function addLocalIP($input){
		$respose = $this->freepbx->sipsettings->getNatObj()->setConfigurations(array($input),"localnets",$this->freepbx);
		return['message' => _('Local IP has been added successfully'),'status' => true];
	}
	
	/**
	 * updateExternalIP
	 *
	 * @param  mixed $input
	 * @return void
	 */
	private function updateExternalIP($input){
		$respose = $this->freepbx->sipsettings->getNatObj()->setConfigurations(array($input['net']),"externip",$this->freepbx);
		return['message' => _('External IP has been updated successfully'),'status' => true];
	}
}
