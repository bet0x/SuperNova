<?php
/**
 * Created by Gorlum 19.08.2016 21:26
 */

namespace Entity;

/**
 * Class KeyedModel
 *
 * @method KeyedContainer fromArray($array)
 *
 * @package Entity
 */

class KeyedModel extends EntityModel {
  protected $newProperties = array(
    'dbId' => array(
      P_DB_FIELD => 'id',
    ),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);
  }

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @param \Entity\KeyedContainer $cEntity
   */
  protected function exportRowNoId($cEntity) {
    $this->exportRow($cEntity);

    if (($idFieldName = $this->getIdFieldName()) != '') {
      unset($cEntity->row[$idFieldName]);
    }
  }

  /**
   * @param int|string $dbId
   *
   * @return KeyedContainer|false
   */
  public function loadById($dbId) {
    $row = $this->rowOperator->getById($this, $dbId);
    if (empty($row)) {
      return false;
    }

    $cEntity = $this->fromArray($row);
    $cEntity->isLoaded = true;

    return $cEntity;
  }

//  protected function load(KeyedContainer $cEntity) {
//    throw new \Exception('EntityModel::dbSave() is not yet implemented');
//  }
//
  protected function delete(KeyedContainer $cEntity) {
    $this->rowOperator->deleteById($this, $cEntity->dbId);
    $cEntity->isLoaded = false;
    $cEntity->isDeleted = true;
    throw new \Exception('KeyedModel::delete() is not yet implemented');
  }

  protected function insert(KeyedContainer $cEntity) {
    $cEntity->dbId = $this->rowOperator->insert($this, $this->exportRowNoId($cEntity));
  }

  protected function update(KeyedContainer $cEntity) {
    // TODO - separate real changes from internal ones
    // Generate changeset row
    // Foreach all rows. If there is change and no delta - then put delta. Otherwise put change
    // If row not empty - update
    throw new \Exception('KeyedModel::update() is not yet implemented');
  }

  protected function unchanged(KeyedContainer $cEntity){
    // TODO - or just save nothing ?????
    // throw new \Exception('EntityModel isNotEmpty, have dbId and not CHANGED! It can\'t be!');
    // Do nothing
  }

  protected function emptyAction(KeyedContainer $cEntity) {
    // Just created container and doesn't use it
//    throw new \Exception('EntityModel isEmpty but not loaded! It can\'t be!');
    // Do nothing
  }

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName() {
    return $this->properties['dbId'][P_DB_FIELD];
  }

}