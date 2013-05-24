<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class AclTest_ContributorRecordsAllow extends Neatline_Case_Default
{


    protected $_isAdminTest = false;


    public function setUp()
    {

        parent::setUp();

        $user1 = $this->loginAsContributor('user1');
        $this->exhibit = $this->__exhibit();
        $this->record1 = $this->__record($this->exhibit);

        $user2 = $this->loginAsContributor('user2');
        $this->record2 = $this->__record($this->exhibit);

        $this->_authenticateUser($user1);

    }


    /**
     * Contributors should be able to create their own records to their
     * own exhibits.
     */
    public function testCanCreateOwnRecordsInOwnExhibits()
    {
        $this->setPost(array('exhibit_id' => $this->exhibit->id));
        $this->dispatch('neatline/records');
        $this->assertNotAction('forbidden');
    }


    /**
     * Contributors should be able to update their own records in their
     * own exhibits.
     */
    public function testCanUpdateOwnRecordsInOwnExhibits()
    {
        $this->setPut(array());
        $this->dispatch('neatline/records/'.$this->record1->id);
        $this->assertNotAction('forbidden');
    }


    /**
     * Contributors should be able to delete their own records in their
     * own exhibits.
     */
    public function testCanDeleteOwnRecordsInOwnExhibits()
    {
        $this->request->setMethod('DELETE');
        $this->dispatch('neatline/records/'.$this->record1->id);
        $this->assertNotAction('forbidden');
    }


    /**
     * Contributors should be able to update other users' records in their
     * own exhibits.
     */
    public function testCanUpdateOtherUsersRecordsInOwnExhibits()
    {
        $this->setPut(array());
        $this->dispatch('neatline/records/'.$this->record2->id);
        $this->assertNotAction('forbidden');
    }


    /**
     * Contributors should be able to delete other users' records in their
     * own exhibits.
     */
    public function testCanDeleteOtherUsersRecordsInOwnExhibits()
    {
        $this->request->setMethod('DELETE');
        $this->dispatch('neatline/records/'.$this->record2->id);
        $this->assertNotAction('forbidden');
    }


}
