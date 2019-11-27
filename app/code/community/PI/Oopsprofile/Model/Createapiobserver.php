<?php
class PI_Oopsprofile_Model_Createapiobserver
{

		public function createapi()
		{

				try
				{
						$apiUserModel = Mage::getModel('api/user');
		
						$apiId = $apiUserModel->getCollection()->addFieldToFilter('username','OopsProfileUser')->getFirstItem()->getId();
						if($apiId=='')//if api user is not exist
						{
								$apiRoleId = $this->createApiRole();

								$this->craeteApiUser($apiUserModel,$apiRoleId);
						}
				}
				catch(Exception $e)
				{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
		}

		
		//code to create role with their resources
		protected function createApiRole()
		{

				//assign resources to role
				$resource = array('_root_',
											'oopsprofile',
											'oopsprofile/exportproductdata',
											'oopsprofile/exportcustomerdata',
											'oopsprofile/exportcustomergroupdata',
											'oopsprofile/exportorderdata',
											'oopsprofile/exportshipmentdata',
											'oopsprofile/exportinvoicedata',
											'oopsprofile/exportcreditmemodata'
											);

				$roleModel = Mage::getModel('api/roles');

				$roleId = $roleModel->getCollection()->addFieldToFilter('role_name','OopsRole')->getFirstItem()->getId();

				if($roleId=='')//if role id is not exist
				{
					
						//create new Role
						$role = $roleModel
				            ->setName('OopsRole')
				            ->setPid(0)
				            ->setRoleType('G')
				            ->save(); //save in api_role

				    Mage::getModel("api/rules")
				        ->setRoleId($role->getId())
				        ->setResources($resource)
				        ->saveRel(); //save resources in api_rule and assign it to a role

				    return $role->getId();
				 }

				 return $this;
		}

		//create api user with their role
		protected function craeteApiUser($apiUserModel,$apiRoleId)
		{
				$userRoles = array ($apiRoleId);
		
				$data['username'] = 'OopsProfileUser';
				$data['firstname'] = 'profile';
				$data['lastname'] = 'user';
				$data['api_key'] = 'oopsprofileapikey';
				$data['is_active'] = 1;
				$data['email'] = 'profileemail@email.com';

				$apiUserModel->setData($data)->save();//save data in api_user

				$role = $apiUserModel->setRoleIds($userRoles)
                      ->setRoleUserId($apiUserModel->getUserId())
                      ->saveRelations(); // save data in api_role and assign role to user

        return $this;
		}
	
}
